# Use the official PHP image from the Docker Hub
FROM php:7.4-cli

# Set the working directory
WORKDIR /var/www

# Copy current directory contents into the container at /var/www
COPY . /var/www

# Expose port 80
EXPOSE 80

# Run the PHP built-in server
CMD ["php", "-S", "0.0.0.0:80", "-t", "/var/www"]
# php-cloudwatch-logging
Example a PHP application logging to CloudWatch

Instructions:
    cp config-sample.php config.php
    (edit config.php to enter your credentials, etc.)
    composer install

Now you can try:

    php log-to-file.php  # logs to a local file
    php log-to-cloudwatch-min.php  # logs to a local file and CloudWatch (minimalist example)
    php log-to-cloudwatch.php       # logs to a local file and CloudWatch


composer install
php8.1 bin/magento setup:upgrade
php8.1 bin/magento setup:di:compile
php8.1 bin/magento setup:static-content:deploy -f
php8.1 bin/magento cache:clean
php8.1 bin/magento cache:enable
php8.1 bin/magento maintenance:disable 
#mv ./app/code/Levinci/Core/Model/FCMService/firebase-service-account-staging.json ./app/code/Levinci/Core/Model/FCMService/firebase-service-account.json

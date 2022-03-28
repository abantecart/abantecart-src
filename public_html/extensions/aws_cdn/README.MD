AWS S3 Integration extension for Abantecart 


Commands for sync: 

```
aws s3 sync /var/www/work/abantecart/abantecart1/image s3://textai-staging-domain-settings/image/ --delete --acl public-read
aws s3 sync /var/www/work/abantecart/abantecart1/resources s3://textai-staging-domain-settings/resources/ --delete --acl public-read
aws s3 sync /var/www/work/abantecart/abantecart1/download s3://textai-staging-domain-settings/download/ --delete --acl public-read
aws s3 sync /var/www/work/abantecart/abantecart1/storefront/ s3://textai-staging-domain-settings/storefront/ --delete --acl public-read --exclude "*" --include "*.png" --include "*.jpg" --include "*.jpeg" --include "*.pdf" --include "*.js" --include "*.css" --include "*.ttf" --include "*.woff*" 
aws s3 sync /var/www/work/abantecart/abantecart1/extensions/ s3://textai-staging-domain-settings/extensions/ --delete --acl public-read --exclude "*" --include "*.png" --include "*.jpg" --include "*.jpeg" --include "*.pdf" --include "*.js" --include "*.css" --include "*.ttf" --include "*.woff*" 
```

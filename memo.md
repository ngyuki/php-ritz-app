# php-ritz-app memo

Docker build.

```sh
docker build . -t ngyuki/php-ritz-app

docker run --rm --name php-ritz-app -p 9876:80 \
  -e APP_ENV=env \
  -e APP_DEBUG=0 \
  -e APP_CACHE_DIR=/tmp \
  ngyuki/php-ritz-app

docker exec php-ritz-app ab -k -c 4 -t 5 http://localhost/
```

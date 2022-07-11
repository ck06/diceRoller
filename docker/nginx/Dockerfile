FROM nginx:alpine

WORKDIR /var/www/current

RUN mkdir -p /var/log/nginx
RUN touch /var/log/nginx/error.log

CMD ["nginx"]

EXPOSE 80 443
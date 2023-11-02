FROM debian:latest

MAINTAINER anton@sijanec.eu

EXPOSE 6844/tcp

WORKDIR /app

RUN apt-get update && apt-get install --no-install-recommends -y php-cli nasm python3 make socat && apt-get clean

COPY ./ /app

CMD ["make", "run" ]

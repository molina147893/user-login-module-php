.PHONY : main build-image build-container start test shell stop clean
main: build-image build-container

build-image:
	docker build -t user-login .

build-container:
	docker run -dt --name user-login -v .:/540/UserLogin user-login
	docker exec user-login composer install

start:
	docker start user-login

test: start
	docker exec user-login ./vendor/bin/phpunit tests/$(target)

shell: start
	docker exec -it user-login /bin/bash

stop:
	docker stop user-login

clean: stop
	docker rm user-login
	rm -rf vendor

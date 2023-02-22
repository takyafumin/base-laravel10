#!/usr/bin/env bash
#
# run.shコマンドを短いコマンドで使う方法
# Bashエイリアスに以下を設定(~/.bashrcなどに設定)
#
#   alias r='[ -f run.sh ] && bash run.sh'
#

PROJECT_NAME=base_laravel10
DIR_PROJECT=laravel-app                                     # Laravel Project Project


OSTYPE="$(uname -s)"
case "${OSTYPE}" in
    Linux*)         MACHINE=linux;;
    Darwin*)        MACHINE=mac;;
    MINGW64_NT*)    MACHINE=win;;
    *)              MACHINE="UNKNOWN"
esac

if [ "$MACHINE" == "UNKNOWN" ]; then
    echo "Unsupported operating system [$(uname -s)]. use Linux / Mac / Windows(git bash) / Windows(WSL2)" >&2
    exit 1
fi

PATH_PREFIX=
CMD_DOCKER="docker-compose"
if [ "$MACHINE" == "win" ]; then
    PATH_PREFIX=/
    CMD_DOCKER="docker-compose"
fi


#------------------
# help表示
#------------------
function display_help {
    echo "Usage:" >&2
    echo "" >&2
    echo "  command [arguments]" >&2
    echo "" >&2
    echo "  if undefined [command] specified, transfer to 'docker-compose' command." >&2
    echo "" >&2
    echo "" >&2
    echo "Commands:" >&2
    echo "" >&2
    echo "  [Environment]" >&2
    echo "    init              Remove the compiled class file" >&2
    echo "    migrate           Exec artisan migrate" >&2
    echo "    seed              Exec artisan db:seed" >&2
    echo "    refresh-db        Exec artisan migrate:refresh --seed" >&2
    echo "    destroy           Exec docker compose down(remove volumes)" >&2
    echo "    destroy-all       Exec docker compose down(remove all resources)" >&2
    echo "" >&2
    echo "  [for Development]" >&2
    echo "    artisan [command] Exec artisan command" >&2
    echo "    tinker            Exec artisan tinker" >&2
    echo "    bash              Exec bash in php container" >&2
    echo "    mysql             Exec mysql cli in mysql container" >&2
    echo "    composer-install  Exec composer install" >&2
    echo "    ide-helper        Generate Laravel IDE Helper File" >&2
    echo "    npm-install       Exec npm run install(npm ci)" >&2
    echo "    npm-dev           Exec npm run dev" >&2
    echo "    npm-watch         Exec npm run watch" >&2
    echo "" >&2
    echo "  [Test & CI]" >&2
    echo "    ci                Exec CI" >&2
    echo "    pint              Code Format Check: php" >&2
    echo "    test              Run phpunit" >&2
    echo "    test-coverage     Run phpunit(Coverage)" >&2
    echo "" >&2
    echo "  [other]" >&2
    echo "    help              Show help" >&2
    echo "" >&2
    echo "" >&2
    exit 0;
}


#------------------
# cleanup local resources
#------------------
function clean_local {
    git clean -fx
    rm -rf vendor/*
    rm -rf node_modules/* node_modules/.bin node_modules/.package-lock.json node_modules/.cache
}


# プロジェクトディレクトリに移動
cd ${DIR_PROJECT}
if [ -f ./.env ]; then
  source ./.env;
fi


#------------------
# Commands
#------------------
if [ $# -eq 0 ]; then
    # show help
    display_help

elif [ "$1" == "help" ]; then
    display_help

elif [ "$1" == "init" ]; then
    # copy .env File
    if [ ! -f .env ]; then
        cp .env.example .env
        source ./.env;
    fi


    # composerライブラリインストール
    docker run \
        --rm --pull=always \
        -v "$(pwd)":/opt \
        -w /opt \
        -e WWWUSER=$WWWUSER -e WWWGROUP=$WWWGROUP \
        -u $WWWUSER:$WWWGROUP \
        laravelsail/php82-composer:latest composer install


    # container build & startup
    ${CMD_DOCKER} build
    ${CMD_DOCKER} up -d


    ## node modulesインストール
    #${CMD_DOCKER} exec ${APP_SERVICE} npm ci


    # mysql起動待ち
    if [ `which mysqladmin` ]; then
        until mysqladmin ping -h 127.0.0.1 -u ${DB_USERNAME} -p${DB_PASSWORD} --silent; do
            echo 'waiting for mysqld...'
            sleep 2
        done
    else
        # 10秒待つ
        sleep 10
    fi


    # Database Migration
    ${CMD_DOCKER} exec -u ${APP_USER} ${APP_SERVICE} php artisan migrate:refresh --seed


    # setup ide-helper
    ${CMD_DOCKER} exec -u ${APP_USER} ${APP_SERVICE} php artisan ide-helper:generate
    ${CMD_DOCKER} exec -u ${APP_USER} ${APP_SERVICE} php artisan ide-helper:model --nowrite


    echo "complete init."

elif [ "$1" == "migrate" ]; then
    ${CMD_DOCKER} exec -u ${APP_USER} ${APP_SERVICE} php artisan migrate

elif [ "$1" == "seed" ]; then
    ${CMD_DOCKER} exec -u ${APP_USER} ${APP_SERVICE} php artisan db:seed

elif [ "$1" == "refresh-db" ]; then
    ${CMD_DOCKER} exec -u ${APP_USER} ${APP_SERVICE} php artisan migrate:refresh --seed

elif [ "$1" == "destroy" ]; then
    ${CMD_DOCKER} down --volumes

elif [ "$1" == "destroy-all" ]; then
    # dockerリソースを削除
    ${CMD_DOCKER} down --rmi all --volumes --remove-orphans

    # ローカルリソースを削除
    clean_local
    echo "complete destroy all."

elif [ "$1" == "up" ]; then
    shift 1;
    ${CMD_DOCKER} up $@

elif [ "$1" == "down" ]; then
    shift 1;
    ${CMD_DOCKER} down $@

elif [ "$1" == "artisan" ] || [ "$1" == "art" ]; then
    shift 1
    ${CMD_DOCKER} exec -u ${APP_USER} ${APP_SERVICE} php artisan "$@"

elif [ "$1" == "tinker" ] || [ "$1" == "t" ]; then
    ${CMD_DOCKER} exec -u ${APP_USER} ${APP_SERVICE} php artisan tinker

elif [ "$1" == "bash" ]; then
    ${CMD_DOCKER} exec -u ${APP_USER} ${APP_SERVICE} bash

elif [ "$1" == "mysql" ]; then
    ${CMD_DOCKER} exec -e MYSQL_PWD=${DB_PASSWORD} \
        ${MYSQL_SERVICE} \
        bash -c 'mysql -u $MYSQL_USER $MYSQL_DATABASE'

elif [ "$1" == "composer-install" ]; then
    ${CMD_DOCKER} exec -u root -e WWWUSER=${WWWUSER} -e WWWGROUP=${WWWGROUP} \
        ${APP_SERVICE} \
        sh -c "composer install && chown -R $WWWUSER:$WWWGROUP ./vendor"

elif [ "$1" == "ide-helper" ]; then
    ${CMD_DOCKER} exec -u ${APP_USER} ${APP_SERVICE} php artisan ide-helper:generate
    ${CMD_DOCKER} exec -u ${APP_USER} ${APP_SERVICE} php artisan ide-helper:model --nowrite

elif [ "$1" == "npm" ]; then
    shift 1;
    ${CMD_DOCKER} exec -u ${APP_USER} ${APP_SERVICE} npm $@

elif [ "$1" == "npm-install" ]; then
    ${CMD_DOCKER} exec -u ${APP_USER} ${APP_SERVICE} npm ci

elif [ "$1" == "npm-dev" ]; then
    ${CMD_DOCKER} exec -u ${APP_USER} ${APP_SERVICE} npm run dev

elif [ "$1" == "npm-watch" ]; then
    ${CMD_DOCKER} exec -u ${APP_USER} ${APP_SERVICE} npm run watch

elif [ "$1" == "ci" ]; then
    ${CMD_DOCKER} exec -u ${APP_USER} ${APP_SERVICE} vendor/bin/pint
    ${CMD_DOCKER} exec -u ${APP_USER} ${APP_SERVICE} bash -c "export XDEBUG_MODE=off && php artisan test"

elif [ "$1" == "pint" ]; then
    shift 1
    ${CMD_DOCKER} exec -u ${APP_USER} ${APP_SERVICE} vendor/bin/pint $@

elif [ "$1" == "test" ]; then
    shift 1
    ${CMD_DOCKER} exec -u ${APP_USER} ${APP_SERVICE} php artisan config:clear
    ${CMD_DOCKER} exec -u ${APP_USER} ${APP_SERVICE} bash -c "export XDEBUG_MODE=off && php artisan test $@"

elif [ "$1" == "test-coverage" ]; then
    ${CMD_DOCKER} exec -u ${APP_USER} ${APP_SERVICE} php artisan config:clear
    ${CMD_DOCKER} exec -u ${APP_USER} ${APP_SERVICE} bash -c "export XDEBUG_MODE=coverage && vendor/bin/phpunit --coverage-html public/coverage"

else
    # transfer to 'docker-compose' command
    ${CMD_DOCKER} "$@"

fi

#!/bin/bash

set -a

function _compose() {
  docker-compose -f docker-compose.yml "$@"
}

function help() { ### Show the list of possible functions - ./app [help]
  grep -E '^function.*?###' $0 \
    | sed "s/^function //g" \
    | sed "s/()/:/g" \
    | awk 'BEGIN {FS = ":(.*?)### "}; {printf "\033[36m%-10s\033[0m %s\n", $1, $2}' \
    | awk 'BEGIN {FS = " - "}; {printf "%-90s \033[90m%s\n", $1, $2}'

    echo -e "\n\033[36mServices available \033[0m"
    _compose ps --services
    echo ""
}

function up() { ### Build and start development environment - ./app up --build --detach
  _compose up "$@"
}

function down() { ### Stop development environment - ./app down
  _compose down
}

function tests() { ### Run all tests (coding standards, static analysis, unit, integration) - ./app tests
  _compose exec php composer run tests
}

function logs() { ### Show container logs - ./app logs <service>
  _compose logs ${1:-}
}

function composer() { ### Execute composer command on php container - ./app composer <command> ...
  _compose exec php composer "$@"
}

function exec() { ### Execute bash command on container - ./app bash <service> <command> ...
  _compose exec ${1} "${@:2}"
}

function ssh() { ### Access a given container (php default) - ./app ssh [<service>]
  _compose exec ${1:-php} bash
}

if [ "${1:- }" = " " ]; then
  help
else
  "$@"
fi

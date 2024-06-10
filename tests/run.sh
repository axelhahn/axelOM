#!/bin/bash
# ======================================================================
#
# TESTS RUNNER
# - Automatically installs codeception if not available.
# - Runs tests with codeception.
#
# ----------------------------------------------------------------------
# ah = <www.axel-hahn.de>
# 2024-06-10 v0.1 ah initial version
# ======================================================================

cd "$( dirname $0 )/.."


# sudo rm -f /usr/local/bin/codecept

if ! which codecept >/dev/null 2>&1; then

    echo "INSTALL Codecept..."
    set -vx
    curl -LsS https://codeception.com/php80/codecept.phar -o /tmp/codecept \
        && sudo mv /tmp/codecept /usr/local/bin/codecept \
        && sudo chmod a+x /usr/local/bin/codecept
    set +vx
    echo

    if ! which codecept >/dev/null 2>&1; then
        echo "Installation failed :-/"
        exit 1
    fi
    echo
    echo "OK: Installation was done. Preparing tests..."
    echo

    codecept bootstrap
    codecept generate:cest Acceptance First

    echo
    exit 0
fi

codecept run --html

# ----------------------------------------------------------------------


#!/bin/sh
#
# Compile small program to calculate raw weather values.
#
# Compiling platform (32bit/64bit) must match servers or values will be different
#
# Live Ryzom shard seems to be using 32bit/no-sse
#
# Compile by running: RYZOM_PATH=$HOME/ryzom.hg/code ./cc.sh
#
# Requires already compiled static NeL libraries under $RYZOM_PATH/build/lib/
#

set -e

if [ -z ${RYZOM_PATH} ]
then
	echo -e "#\n# ERR: RYZOM_PATH env variable is not set\n#"
	exit 1
fi

echo "sources   : ${RYZOM_PATH}"
echo "libraries : ${RYZOM_PATH}/build"

OPTS="-O3"
LIBS="-lnelmisc -lc -lpthread -lrt -ldl"

g++ -DNL_RELEASE -I${RYZOM_PATH}/nel/include/ -L${RYZOM_PATH}/build/lib ${OPTS} main.cpp ${LIBS} -o raw-weather


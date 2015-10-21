cd "$REPO_HOME/STL Modern Mopar/content/"

sass --update resources/sass:resources/css

cd "$REPO_HOME"

cp -r "STL Modern Mopar" "C:/Bitnami/wampstack-7.0.0RC5-0/apps/"

cd "$REPO_HOME/STL Modern Mopar/content/resources/"

rm -rf css
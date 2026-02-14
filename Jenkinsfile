pipeline {
    agent any

    environment {
        IMAGE_NAME = "school-erp-app"
        IMAGE_TAG  = "${env.GIT_COMMIT}"
        CONTAINER  = "school_erp_app"
        ENV_PATH   = "/opt/school-erp/src/.env"
    }

    stages {

        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Build Image') {
            steps {
                sh '''
                    docker build \
                      --pull \
                      --no-cache \
                      -t ${IMAGE_NAME}:${IMAGE_TAG} \
                      -t ${IMAGE_NAME}:latest \
                      -f docker/Dockerfile .
                '''
            }
        }

        stage('Deploy') {
            steps {
                sh '''
                    docker stop ${CONTAINER} || true
                    docker rm ${CONTAINER} || true

                    docker run -d \
                      --name ${CONTAINER} \
                      --restart=always \
                      -p 127.0.0.1:9001:9000 \
                      -v school_storage:/var/www/storage \
                      -v ${ENV_PATH}:/var/www/.env \
                      --network school_erp_network \
                      --network mysql_default \
                      ${IMAGE_NAME}:${IMAGE_TAG}
                '''
            }
        }

        stage('Migrate & Optimize') {
            steps {
                sh '''
                    docker exec ${CONTAINER} php artisan migrate --force
                    docker exec ${CONTAINER} php artisan config:clear
                    docker exec ${CONTAINER} php artisan config:cache
                    docker exec ${CONTAINER} php artisan view:cache
                '''
            }
        }
    }
}

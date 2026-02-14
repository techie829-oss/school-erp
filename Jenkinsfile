pipeline {
    agent any

    environment {
        IMAGE_NAME = "school-erp-app"
        NGINX_IMAGE = "school-erp-nginx"
        IMAGE_TAG = "${env.GIT_COMMIT}"
        ENV_FILE = "${WORKSPACE}/.env"
    }

    stages {

        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Inject ENV') {
            steps {
                withCredentials([file(credentialsId: 'school-erp-env', variable: 'ENVFILE')]) {
                    sh 'cp $ENVFILE .env'
                }
            }
        }

        stage('Build Images') {
            steps {
                sh '''
                    docker build --pull --no-cache \
                      -t ${IMAGE_NAME}:${IMAGE_TAG} \
                      -t ${IMAGE_NAME}:latest \
                      -f docker/Dockerfile .

                    docker build --pull --no-cache \
                      -t ${NGINX_IMAGE}:${IMAGE_TAG} \
                      -t ${NGINX_IMAGE}:latest \
                      -f docker/nginx/Dockerfile .
                '''
            }
        }

        stage('Deploy') {
            steps {
                sh '''
                    export ENV_FILE=${ENV_FILE}
                    docker-compose down || true
                    docker-compose up -d --build
                '''
            }
        }

        stage('Migrate & Optimize') {
            steps {
                sh '''
                    docker exec school_erp_app php artisan migrate --force
                    docker exec school_erp_app php artisan config:cache
                    docker exec school_erp_app php artisan route:cache
                    docker exec school_erp_app php artisan view:cache
                '''
            }
        }
    }
}

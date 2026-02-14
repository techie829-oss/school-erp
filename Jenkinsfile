pipeline {
    agent any

    environment {
        IMAGE_NAME = "school-erp-app"
        IMAGE_TAG  = "${env.GIT_COMMIT}"
        CONTAINER  = "school_erp_app"
        // Using existing path for now, but abstracted for portability
        ENV_PATH   = "/opt/school-erp/src/.env"
    }

    stages {

        stage('Checkout Code') {
            steps {
                checkout scm
            }
        }

        stage('Build Fresh Image') {
            steps {
                sh '''
                    docker build \\
                      --pull \\
                      --no-cache \\
                      -t ${IMAGE_NAME}:${IMAGE_TAG} \\
                      -t ${IMAGE_NAME}:latest \\
                      -f docker/Dockerfile .
                '''
            }
        }

        stage('Deploy Container') {
            steps {
                sh '''
                    docker stop ${CONTAINER} || true
                    docker rm ${CONTAINER} || true

                    # Ensure network exists
                    docker network inspect school_erp_network >/dev/null 2>&1 || docker network create school_erp_network

                    docker run -d \\
                      --name ${CONTAINER} \\
                      --restart=always \\
                      -p 127.0.0.1:9001:9000 \\
                      -v school_storage:/var/www/storage \
                      --env-file ${ENV_PATH} \
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
                    docker exec ${CONTAINER} php artisan config:cache
                    docker exec ${CONTAINER} php artisan route:cache
                    docker exec ${CONTAINER} php artisan view:cache
                '''
            }
        }
    }

    post {
        success {
            echo "Deployment Successful - Clean Architecture"
        }
        failure {
            echo "Deployment Failed"
        }
    }
}

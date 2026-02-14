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

        stage('Build Images') {
            steps {
                sh '''
                    # Build App Image
                    docker build \
                      --pull \
                      --no-cache \
                      -t ${IMAGE_NAME}:${IMAGE_TAG} \
                      -t ${IMAGE_NAME}:latest \
                      -f docker/Dockerfile .

                    # Build Nginx Image
                    docker build \
                      --pull \
                      --no-cache \
                      -t school-erp-nginx:${IMAGE_TAG} \
                      -t school-erp-nginx:latest \
                      -f docker/nginx/Dockerfile .
                '''
            }
        }

        stage('Deploy Containers') {
            steps {
                sh '''
                    # Clean up old containers
                    docker stop school_erp_nginx || true
                    docker rm school_erp_nginx || true
                    docker stop ${CONTAINER} || true
                    docker rm ${CONTAINER} || true

                    # Ensure network exists
                    docker network inspect school_erp_network >/dev/null 2>&1 || docker network create school_erp_network

                    # 1. Run App Container (Mount Host Code + Preserve Image Builds)
                    docker run -d \
                      --name ${CONTAINER} \
                      --restart=always \
                      -v $(pwd):/var/www \
                      -v /var/www/vendor \
                      -v /var/www/node_modules \
                      -v /var/www/public/build \
                      -v school_storage:/var/www/storage \
                      -v ${ENV_PATH}:/var/www/.env \
                      --network school_erp_network \
                      --network mysql_default \
                      ${IMAGE_NAME}:${IMAGE_TAG}

                    # 2. Run Nginx Container (Share App Filesystem)
                    docker run -d \
                      --name school_erp_nginx \
                      --restart=always \
                      -p 127.0.0.1:9001:80 \
                      --volumes-from ${CONTAINER} \
                      --network school_erp_network \
                      school-erp-nginx:${IMAGE_TAG}
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

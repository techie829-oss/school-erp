pipeline {
    agent any

    environment {
        APP_CONTAINER = "school_erp_app"
        PROJECT_PATH = "/opt/school-erp/src"
    }

    stages {

        stage('Checkout Code') {
            steps {
                checkout scm
            }
        }

        stage('Sync Code Safely') {
            steps {
                sh """
                    rsync -av --delete \
                        --exclude='.env' \
                        --exclude='storage' \
                        --exclude='.git' \
                        ./ ${PROJECT_PATH}/
                """
            }
        }

        stage('Composer Install') {
            steps {
                sh """
                    docker exec ${APP_CONTAINER} composer install --no-dev --optimize-autoloader
                """
            }
        }

        stage('Run Migrations') {
            steps {
                sh """
                    docker exec ${APP_CONTAINER} php artisan migrate --force
                """
            }
        }

        stage('Optimize Application') {
            steps {
                sh """
                    docker exec ${APP_CONTAINER} php artisan config:cache
                    docker exec ${APP_CONTAINER} php artisan route:cache
                    docker exec ${APP_CONTAINER} php artisan view:cache
                """
            }
        }
    }

    post {
        success {
            echo "Deployment Successful - No Data Lost"
        }
        failure {
            echo "Deployment Failed - Review Logs"
        }
    }
}

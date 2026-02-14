pipeline {
    agent any

    environment {
        ENV_FILE = "/opt/school-erp/src/.env"
    }

    stages {

        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Build & Deploy') {
            steps {
                sh """
                    docker compose down || true

                    docker compose \
                        --env-file ${ENV_FILE} \
                        up -d --build --remove-orphans
                """
            }
        }

        stage('Migrate & Optimize') {
            steps {
                sh '''
                    docker exec school_erp_app php artisan migrate --force
                    docker exec school_erp_app php artisan config:cache
                    docker exec school_erp_app php artisan route:cache || true
                    docker exec school_erp_app php artisan view:cache
                '''
            }
        }
    }
}

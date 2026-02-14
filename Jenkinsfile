pipeline {
    agent any

    environment {
        APP_CONTAINER = "school_erp_app"
    }

    stages {

        stage('Checkout Code') {
            steps {
                checkout scm
            }
        }

        stage('Build & Deploy') {
            steps {
                // Safer rolling update style
                sh 'docker compose up -d --build app'
            }
        }

        stage('Run Migrations') {
            steps {
                sh "docker exec ${APP_CONTAINER} php artisan migrate --force"
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
            echo "Deployment Successful - Image Based"
        }
        failure {
            echo "Deployment Failed"
        }
    }
}

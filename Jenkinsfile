pipeline {
    agent any

    environment {
        IMAGE_NAME = "school-erp"
    }

    stages {

        stage('Checkout Code') {
            steps {
                checkout scm
            }
        }

        stage('Inject ENV') {
            steps {
                withCredentials([file(credentialsId: 'school-erp-env', variable: 'ENV_FILE')]) {
                    sh 'cp $ENV_FILE .env'
                }
            }
        }

        stage('Build & Deploy') {
            steps {
                sh '''
                    docker compose down || true
                    docker compose up -d --build --remove-orphans
                '''
            }
        }

        stage('Migrate & Optimize') {
            steps {
                sh '''
                    docker compose exec app php artisan migrate --force
                    docker compose exec app php artisan config:cache
                    docker compose exec app php artisan route:cache
                    docker compose exec app php artisan view:cache
                '''
            }
        }
    }

    post {
        success {
            echo "Deployment Successful"
        }
        failure {
            echo "Deployment Failed"
        }
    }
}

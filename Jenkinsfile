pipeline {
    agent any

    stages {

        stage('Checkout Code') {
            steps {
                checkout scm
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

        stage('Run Migrations') {
            steps {
                sh 'docker exec school_erp_app php artisan migrate --force'
            }
        }

        stage('Optimize Laravel') {
            steps {
                sh '''
                    docker exec school_erp_app php artisan config:cache
                    docker exec school_erp_app php artisan route:cache || true
                    docker exec school_erp_app php artisan view:cache
                '''
            }
        }
    }
}

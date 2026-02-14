pipeline {
    agent any

    stages {

        stage('Checkout Code') {
            steps {
                checkout scm
            }
        }

        stage('Deploy') {
            steps {
                sh 'docker compose up -d --build --remove-orphans'
            }
        }



        stage('Run Migrations') {
            steps {
                sh 'docker exec school_erp_app php artisan migrate --force'
            }
        }

        stage('Optimize Application') {
            steps {
                sh '''
                    docker exec school_erp_app php artisan config:cache
                    docker exec school_erp_app php artisan route:cache
                    docker exec school_erp_app php artisan view:cache
                '''
            }
        }
    }

    post {
        success {
            echo "Deployment Successful - Pure Docker Mode"
        }
        failure {
            echo "Deployment Failed"
        }
    }
}

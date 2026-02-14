pipeline {
    agent any

    stages {

        // 1. Checkout the code from the Git repository
        stage('Checkout Code') {
            steps {
                checkout scm
            }
        }

        // 2. Deploy: Build image & deploy containers with zero downtime (orphans removed)
        stage('Deploy') {
            steps {
                sh 'docker-compose up -d --build --remove-orphans'
            }
        }

        // 3. Post-Deployment: Run database migrations forcefully
        stage('Run Migrations') {
            steps {
                sh 'docker exec school_erp_app php artisan migrate --force'
            }
        }

        // 4. Optimization: Cache config, routes, and views for production performance
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

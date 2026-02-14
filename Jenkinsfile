pipeline {
    agent any

    stages {

        stage('Pull Latest Code') {
            steps {
                sh '''
                    cd /opt/school-erp
                    git pull origin main
                '''
            }
        }

        stage('Build & Deploy') {
            steps {
                sh '''
                    cd /opt/school-erp
                    docker compose up -d --build --remove-orphans
                '''
            }
        }

        stage('Run Migrations') {
            steps {
                sh '''
                    docker exec school_erp_app php artisan migrate --force
                '''
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

    post {
        success {
            echo "Deployment Successful"
        }
        failure {
            echo "Deployment Failed"
        }
    }
}

pipeline {
    agent any

    stages {

        stage('Checkout Code') {
            steps {
                checkout scm
            }
        }

        stage('Build Image') {
            steps {
                sh '''
                    docker build \\
                      -t school-erp-app:latest \\
                      -f docker/Dockerfile .
                '''
            }
        }

        stage('Deploy Container') {
            steps {
                sh '''
                    docker stop school_erp_app || true
                    docker rm school_erp_app || true

                    docker run -d \
                    --name school_erp_app \
                    --restart=always \
                    -p 127.0.0.1:9001:9000 \
                    -v school_storage:/var/www/storage \
                    --network school_erp_network \
                    --network mysql_default \
                    --env-file /opt/school-erp/src/.env \
                    school-erp-app:latest
                '''
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

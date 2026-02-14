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

        stage('Deploy') {
            steps {
                sh 'docker-compose up -d --build --remove-orphans'
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

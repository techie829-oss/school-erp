pipeline {
    agent any

    stages {

        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Inject ENV') {
            steps {
                withCredentials([file(credentialsId: 'school-erp-env', variable: 'ENV_FILE')]) {
                    sh '''
                        # Remove existing .env file if present
                        rm -f .env
                        
                        # Copy the env file using cat to avoid permission issues
                        cat "$ENV_FILE" > .env
                        
                        # Set appropriate permissions
                        chmod 644 .env
                        
                        # Verify the file was created
                        if [ ! -f .env ]; then
                            echo "ERROR: Failed to create .env file"
                            exit 1
                        fi
                        
                        echo "✓ .env file created successfully"
                    '''
                }
            }
        }

        stage('Build & Deploy') {
            steps {
                sh '''
                    # Stop and remove any existing containers with the same name
                    echo "Cleaning up old containers..."
                    docker stop school_erp_app school_erp_nginx 2>/dev/null || true
                    docker rm school_erp_app school_erp_nginx 2>/dev/null || true
                    
                    # Stop compose managed containers
                    docker compose down || true
                    
                    # Build and start containers
                    echo "Building and starting containers..."
                    docker compose up -d --build
                    
                    # Wait for containers to be healthy
                    echo "Waiting for containers to start..."
                    sleep 10
                    
                    # Verify containers are running
                    echo "✓ Containers started successfully"
                    docker compose ps
                '''
            }
        }

        stage('Migrate & Optimize') {
            steps {
                sh '''
                    echo "Running database migrations..."
                    docker compose exec -T app php artisan migrate --force
                    
                    echo "Caching configuration..."
                    docker compose exec -T app php artisan config:cache
                    
                    echo "Caching routes..."
                    docker compose exec -T app php artisan route:cache
                    
                    echo "Caching views..."
                    docker compose exec -T app php artisan view:cache
                    
                    echo "✓ Database migrations and optimizations completed"
                '''
            }
        }
    }

    post {
        success {
            echo "✓ Deployment Successful"
            sh '''
                echo ""
                echo "Application is running at: http://127.0.0.1:9001"
                docker compose ps
            '''
        }
        failure {
            echo "✗ Deployment Failed"
            sh '''
                echo ""
                echo "Container Status:"
                docker compose ps || true
                docker ps -a --filter "name=school_erp" || true
                echo ""
                echo "Recent Logs:"
                docker compose logs --tail=50 || true
            '''
        }
        always {
            echo "Pipeline execution completed"
        }
    }
}
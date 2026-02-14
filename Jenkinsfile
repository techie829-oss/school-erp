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
                    # Stop existing containers
                    docker compose down || true
                    
                    # Build and start containers
                    docker compose up -d --build
                    
                    # Wait for containers to be healthy
                    echo "Waiting for containers to start..."
                    sleep 10
                    
                    # Verify containers are running
                    docker compose ps
                '''
            }
        }

        stage('Migrate & Optimize') {
            steps {
                sh '''
                    # Run migrations
                    docker compose exec -T app php artisan migrate --force
                    
                    # Cache configuration
                    docker compose exec -T app php artisan config:cache
                    
                    # Cache routes
                    docker compose exec -T app php artisan route:cache
                    
                    # Cache views
                    docker compose exec -T app php artisan view:cache
                    
                    echo "✓ Database migrations and optimizations completed"
                '''
            }
        }
    }

    post {
        success {
            echo "✓ Deployment Successful"
        }
        failure {
            echo "✗ Deployment Failed"
            sh '''
                echo "Container Status:"
                docker compose ps || true
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
pipeline {
    agent any

    options {
        // Clean workspace before build to ensure fresh checkout
        skipDefaultCheckout(true)
    }

    stages {

        stage('Clean Workspace') {
            steps {
                // Clean workspace to ensure no stale files
                cleanWs()
            }
        }

        stage('Checkout') {
            steps {
                // Fresh checkout from repository
                checkout scm
                
                sh '''
                    echo "======================================"
                    echo "Repository checked out successfully"
                    echo "======================================"
                    echo ""
                    echo "Verifying critical files..."
                    
                    # Check for nginx config
                    if [ -f docker/nginx/default.conf ]; then
                        echo "✓ docker/nginx/default.conf found"
                    else
                        echo "✗ ERROR: docker/nginx/default.conf NOT FOUND"
                        echo "Listing docker directory contents:"
                        find docker -type f 2>/dev/null || echo "No docker directory found"
                        exit 1
                    fi
                    
                    # Check other required files
                    [ -f docker/Dockerfile ] && echo "✓ docker/Dockerfile found" || (echo "✗ docker/Dockerfile missing" && exit 1)
                    [ -f docker-compose.yml ] && echo "✓ docker-compose.yml found" || (echo "✗ docker-compose.yml missing" && exit 1)
                    
                    echo ""
                    echo "All required files present!"
                '''
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
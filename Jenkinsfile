pipeline {
    agent any

    options {
        skipDefaultCheckout(true)
    }

    stages {

        stage('Clean Workspace') {
            steps {
                cleanWs()
            }
        }

        stage('Checkout') {
            steps {
                checkout scm
                
                sh '''
                    echo "======================================"
                    echo "Repository checked out successfully"
                    echo "======================================"
                    echo ""
                    echo "Detailed file verification..."
                    
                    # Check file type (not just existence)
                    if [ -f docker/nginx/default.conf ]; then
                        echo "✓ docker/nginx/default.conf is a FILE"
                        ls -lh docker/nginx/default.conf
                        file docker/nginx/default.conf
                    elif [ -d docker/nginx/default.conf ]; then
                        echo "✗ ERROR: docker/nginx/default.conf is a DIRECTORY (should be a file)"
                        ls -la docker/nginx/default.conf/
                        exit 1
                    else
                        echo "✗ ERROR: docker/nginx/default.conf NOT FOUND"
                        exit 1
                    fi
                    
                    # Verify other files
                    [ -f docker/Dockerfile ] && echo "✓ docker/Dockerfile found" || (echo "✗ docker/Dockerfile missing" && exit 1)
                    [ -f docker-compose.yml ] && echo "✓ docker-compose.yml found" || (echo "✗ docker-compose.yml missing" && exit 1)
                    
                    echo ""
                    echo "All required files present and valid!"
                '''
            }
        }

        stage('Inject ENV') {
            steps {
                withCredentials([file(credentialsId: 'school-erp-env', variable: 'ENV_FILE')]) {
                    sh '''
                        rm -f .env
                        cat "$ENV_FILE" > .env
                        chmod 644 .env
                        
                        if [ ! -f .env ]; then
                            echo "ERROR: Failed to create .env file"
                            exit 1
                        fi
                        
                        echo "✓ .env file created successfully"
                    '''
                }
            }
        }

        stage('Cleanup Docker State') {
            steps {
                sh '''
                    echo "Performing thorough cleanup..."
                    
                    # Stop and remove containers
                    docker stop school_erp_app school_erp_nginx 2>/dev/null || true
                    docker rm -f school_erp_app school_erp_nginx 2>/dev/null || true
                    
                    # Remove compose resources
                    docker compose down -v 2>/dev/null || true
                    
                    # Clean up any orphaned volumes that might have file conflicts
                    docker volume ls -q | grep school || true
                    
                    echo "✓ Cleanup completed"
                '''
            }
        }

        stage('Build & Deploy') {
            steps {
                sh '''
                    echo "Building and starting containers..."
                    
                    # Verify the nginx config file one more time before docker compose
                    if [ ! -f docker/nginx/default.conf ]; then
                        echo "ERROR: docker/nginx/default.conf disappeared!"
                        exit 1
                    fi
                    
                    # Show absolute path for debugging
                    echo "Nginx config absolute path: $(pwd)/docker/nginx/default.conf"
                    
                    # Start containers
                    docker compose up -d --build
                    
                    # Wait for containers
                    echo "Waiting for containers to start..."
                    sleep 10
                    
                    # Verify both containers are running
                    if ! docker ps | grep -q school_erp_app; then
                        echo "ERROR: school_erp_app container failed to start"
                        docker logs school_erp_app 2>&1 || true
                        exit 1
                    fi
                    
                    if ! docker ps | grep -q school_erp_nginx; then
                        echo "ERROR: school_erp_nginx container failed to start"
                        docker logs school_erp_nginx 2>&1 || true
                        exit 1
                    fi
                    
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
                echo "========================================="
                echo "Application is running at: http://127.0.0.1:9001"
                echo "========================================="
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
                echo "App Container Logs:"
                docker logs school_erp_app 2>&1 | tail -50 || true
                echo ""
                echo "Nginx Container Logs:"
                docker logs school_erp_nginx 2>&1 | tail -50 || true
            '''
        }
        always {
            echo "Pipeline execution completed"
        }
    }
}
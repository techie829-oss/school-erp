pipeline {
    agent any

    environment {
        // Fixed deployment directory on HOST (not in Jenkins workspace)
        DEPLOY_DIR = '/opt/deployments/school-erp'
        GIT_REPO = 'git@github.com:techie829-oss/school-erp.git'
        GIT_BRANCH = 'main'
    }

    stages {

        stage('Prepare Deploy Directory') {
            steps {
                sh '''
                    echo "======================================"
                    echo "Preparing deployment directory on host"
                    echo "======================================"
                    
                    # Create deployment directory if it doesn't exist
                    # Note: This runs on HOST because Jenkins has docker socket mounted
                    docker run --rm \
                        -v /opt:/opt \
                        alpine:latest \
                        sh -c "mkdir -p ${DEPLOY_DIR} && chown -R 1000:1000 ${DEPLOY_DIR}" || true
                    
                    echo "✓ Deployment directory ready: ${DEPLOY_DIR}"
                '''
            }
        }

        stage('Deploy Code to Host') {
            steps {
                sh '''
                    echo "======================================"
                    echo "Deploying code to: ${DEPLOY_DIR}"
                    echo "======================================"
                    
                    # Use a git container to clone/pull code directly to host path
                    # This avoids the Jenkins workspace entirely
                    
                    # Check if git repo exists
                    if docker run --rm -v ${DEPLOY_DIR}:${DEPLOY_DIR} -w ${DEPLOY_DIR} alpine:latest test -d .git; then
                        echo "Repository exists, pulling latest changes..."
                        docker run --rm \
                            -v ${DEPLOY_DIR}:${DEPLOY_DIR} \
                            -v /home/jadmin/.ssh:/root/.ssh:ro \
                            -w ${DEPLOY_DIR} \
                            alpine/git:latest \
                            pull origin ${GIT_BRANCH}
                    else
                        echo "Cloning repository..."
                        docker run --rm \
                            -v ${DEPLOY_DIR}:/git \
                            -v /home/jadmin/.ssh:/root/.ssh:ro \
                            alpine/git:latest \
                            clone ${GIT_REPO} /git
                        
                        # Checkout correct branch
                        docker run --rm \
                            -v ${DEPLOY_DIR}:${DEPLOY_DIR} \
                            -w ${DEPLOY_DIR} \
                            alpine/git:latest \
                            checkout ${GIT_BRANCH}
                    fi
                    
                    echo "✓ Code deployed successfully"
                    
                    # Verify critical files exist
                    echo ""
                    echo "Verifying deployment..."
                    docker run --rm -v ${DEPLOY_DIR}:${DEPLOY_DIR} -w ${DEPLOY_DIR} alpine:latest sh -c '
                        if [ -f docker/nginx/default.conf ]; then
                            echo "✓ docker/nginx/default.conf found"
                        else
                            echo "✗ docker/nginx/default.conf missing"
                            exit 1
                        fi
                        
                        if [ -f docker/Dockerfile ]; then
                            echo "✓ docker/Dockerfile found"
                        else
                            echo "✗ docker/Dockerfile missing"
                            exit 1
                        fi
                        
                        if [ -f docker-compose.yml ]; then
                            echo "✓ docker-compose.yml found"
                        else
                            echo "✗ docker-compose.yml missing"
                            exit 1
                        fi
                    '
                '''
            }
        }

        stage('Inject ENV') {
            steps {
                withCredentials([file(credentialsId: 'school-erp-env', variable: 'ENV_FILE')]) {
                    sh '''
                        echo "Injecting environment file..."
                        
                        # Copy .env file to deployment directory on host
                        docker run --rm \
                            -v ${ENV_FILE}:/tmp/env:ro \
                            -v ${DEPLOY_DIR}:${DEPLOY_DIR} \
                            -w ${DEPLOY_DIR} \
                            alpine:latest \
                            sh -c "cat /tmp/env > .env && chmod 644 .env"
                        
                        # Verify .env was created
                        docker run --rm \
                            -v ${DEPLOY_DIR}:${DEPLOY_DIR} \
                            -w ${DEPLOY_DIR} \
                            alpine:latest \
                            test -f .env || (echo "ERROR: .env file not created" && exit 1)
                        
                        echo "✓ .env file injected successfully"
                    '''
                }
            }
        }

        stage('Build & Deploy') {
            steps {
                sh '''
                    echo "======================================"
                    echo "Building and deploying application"
                    echo "======================================"
                    
                    # Stop old containers
                    echo "Stopping existing containers..."
                    docker stop school_erp_app school_erp_nginx 2>/dev/null || true
                    docker rm -f school_erp_app school_erp_nginx 2>/dev/null || true
                    
                    # Run docker compose from the deployment directory on host
                    # Use docker run to execute docker-compose with proper working directory
                    docker run --rm \
                        -v /var/run/docker.sock:/var/run/docker.sock \
                        -v ${DEPLOY_DIR}:${DEPLOY_DIR} \
                        -w ${DEPLOY_DIR} \
                        docker/compose:latest \
                        down || true
                    
                    echo "Starting new containers..."
                    docker run --rm \
                        -v /var/run/docker.sock:/var/run/docker.sock \
                        -v ${DEPLOY_DIR}:${DEPLOY_DIR} \
                        -w ${DEPLOY_DIR} \
                        docker/compose:latest \
                        up -d --build
                    
                    echo "Waiting for containers to start..."
                    sleep 10
                    
                    # Verify containers are running
                    docker run --rm \
                        -v /var/run/docker.sock:/var/run/docker.sock \
                        -v ${DEPLOY_DIR}:${DEPLOY_DIR} \
                        -w ${DEPLOY_DIR} \
                        docker/compose:latest \
                        ps
                    
                    echo "✓ Containers started successfully"
                '''
            }
        }

        stage('Migrate & Optimize') {
            steps {
                sh '''
                    echo "======================================"
                    echo "Running migrations and optimizations"
                    echo "======================================"
                    
                    # Run migrations
                    echo "Running database migrations..."
                    docker exec school_erp_app php artisan migrate --force
                    
                    # Cache optimizations
                    echo "Caching configuration..."
                    docker exec school_erp_app php artisan config:cache
                    
                    echo "Caching routes..."
                    docker exec school_erp_app php artisan route:cache
                    
                    echo "Caching views..."
                    docker exec school_erp_app php artisan view:cache
                    
                    echo "✓ Migrations and optimizations completed"
                '''
            }
        }
    }

    post {
        success {
            echo "========================================="
            echo "✓ Deployment Successful!"
            echo "========================================="
            sh '''
                echo ""
                echo "Application URL: http://127.0.0.1:9001"
                echo "Deployment location: ${DEPLOY_DIR}"
                echo ""
                echo "Container Status:"
                docker ps --filter "name=school_erp" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
            '''
        }
        failure {
            echo "========================================="
            echo "✗ Deployment Failed"
            echo "========================================="
            sh '''
                echo ""
                echo "Container Status:"
                docker ps -a --filter "name=school_erp" --format "table {{.Names}}\t{{.Status}}" || true
                echo ""
                echo "Recent Logs:"
                docker logs --tail=50 school_erp_app 2>&1 || true
                docker logs --tail=50 school_erp_nginx 2>&1 || true
            '''
        }
        always {
            echo "Pipeline execution completed"
        }
    }
}
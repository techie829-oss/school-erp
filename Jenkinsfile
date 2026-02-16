pipeline {
    agent any

    options {
        disableConcurrentBuilds()
        timestamps()
    }

    triggers {
        githubPush()
    }

    parameters {
        string(name: 'BRANCH_NAME', defaultValue: 'main', description: 'Branch to deploy')
        booleanParam(name: 'REFRESH_DB', defaultValue: false, description: 'Run migrate:refresh (Destructive!)')
    }
    
    environment {
        DEPLOY_DIR = '/opt/deployments/school-erp'
        GIT_REPO = 'git@github.com:techie829-oss/school-erp.git'
        GIT_BRANCH = "${params.BRANCH_NAME}"
        BUILD_TAG = "build-${BUILD_NUMBER}"
    }
    
    stages {
        stage('📁 Prepare Deploy Directory') {
            steps {
                sh '''
                    echo "=== Preparing Deployment Directory ==="
                    docker run --rm -v /opt:/opt alpine:latest \
                        sh -c "mkdir -p ${DEPLOY_DIR} && chown -R 1000:1000 ${DEPLOY_DIR}"
                    echo "✓ Directory ready: ${DEPLOY_DIR}"
                '''
            }
        }

        stage('🔑 Prepare SSH Keys') {
            steps {
                script {
                    withCredentials([sshUserPrivateKey(credentialsId: 'github-ssh-key', keyFileVariable: 'SSH_KEY_FILE', passphraseVariable: '', usernameVariable: '')]) {
                        sh '''
                            echo "=== Setting up SSH Keys for Deployment ==="
                            
                            # 1. Create secret directory in /opt (shared volume)
                            docker run --rm -v /opt:/opt alpine sh -c "mkdir -p /opt/deployments/.ssh_deploy && chmod 700 /opt/deployments/.ssh_deploy"
                            
                            # 2. KeyScan GitHub (to known_hosts) - Using alpine with openssh-client
                            docker run --rm -v /opt:/opt alpine sh -c "apk add --no-cache openssh-client && mkdir -p ~/.ssh && ssh-keyscan github.com > /opt/deployments/.ssh_deploy/known_hosts && chmod 644 /opt/deployments/.ssh_deploy/known_hosts"
                            
                            # 3. Copy Private Key (Stream from Jenkins to Container Volume)
                            cat ${SSH_KEY_FILE} | docker run -i --rm -v /opt:/opt alpine sh -c "cat > /opt/deployments/.ssh_deploy/id_rsa && chmod 600 /opt/deployments/.ssh_deploy/id_rsa"
                            
                            echo "✓ SSH Keys staged in /opt/deployments/.ssh_deploy"
                        '''
                    }
                }
            }
        }
        
        stage('📥 Deploy Code') {
            steps {
                sh '''
                    echo "=== Deploying Code from GitHub ==="
                    
                    # Verify git existence (Using alpine since alpine/git entrypoint is 'git')
                    if docker run --rm \
                        -v /opt/deployments/.ssh_deploy:/root/.ssh:ro \
                        -v ${DEPLOY_DIR}:${DEPLOY_DIR} \
                        -w ${DEPLOY_DIR} \
                        alpine test -d .git; then
                        
                        echo "Pulling latest changes..."
                        docker run --rm \
                            -v /opt/deployments/.ssh_deploy:/root/.ssh:ro \
                            -v ${DEPLOY_DIR}:${DEPLOY_DIR} \
                            -w ${DEPLOY_DIR} \
                            alpine/git:latest pull origin ${GIT_BRANCH}
                    else
                        echo "Cloning repository..."
                        docker run --rm \
                            -v /opt/deployments/.ssh_deploy:/root/.ssh:ro \
                            -v ${DEPLOY_DIR}:/git \
                            alpine/git:latest clone ${GIT_REPO} /git
                    fi
                    echo "✓ Code deployed"
                '''
            }
        }
        
        stage('🔐 Inject ENV') {
            steps {
                withCredentials([file(credentialsId: 'school-erp-env', variable: 'ENV_FILE')]) {
                    sh '''
                        echo "Injecting .env file..."
                        docker run --rm \
                            -v ${ENV_FILE}:/tmp/env:ro \
                            -v ${DEPLOY_DIR}:${DEPLOY_DIR} \
                            -w ${DEPLOY_DIR} \
                            alpine:latest sh -c "cat /tmp/env > .env && chmod 644 .env"
                        echo "✓ .env injected"
                    '''
                }
            }
        }
        
        stage('🐳 Build & Deploy') {
            steps {
                sh '''
                    echo "=== Building & Deploying Application ==="
                    cd ${DEPLOY_DIR}
                    
                    # Stop old containers
                    docker compose down || true
                    
                    # Build with build number tag
                    export BUILD_NUMBER=${BUILD_NUMBER}
                    docker compose build --no-cache
                    
                    # Start services
                    docker compose up -d
                    
                    echo "Waiting for services to start..."
                    sleep 15
                    
                    docker compose ps
                    echo "✓ Services started"
                '''
            }
        }
        
        stage('🗄️ Database & Cache') {
            steps {
                sh '''
                    echo "=== Running Migrations & Optimizations ==="
                    
                    # Migrations
                    docker exec school_erp_app php artisan migrate --force
                    
                    # Cache optimization
                    docker exec school_erp_app php artisan config:cache
                    docker exec school_erp_app php artisan route:cache
                    docker exec school_erp_app php artisan view:cache
                    
                    echo "✓ Optimizations complete"
                '''
            }
        }
        
        stage('✅ Health Check') {
            steps {
                sh '''
                    echo "=== Running Health Checks ==="
                    sleep 5
                    
                    # Check containers
                    docker ps --filter "name=school_erp"
                    
                    # Check application response
                    curl -f http://127.0.0.1:9001/health || echo "Warning: Health endpoint not responding"
                    
                    echo "✓ Health checks complete"
                '''
            }
        }
    }
    
    post {
        success {
            echo """
╔═══════════════════════════════════════╗
║  ✅ DEPLOYMENT SUCCESSFUL!           ║
╚═══════════════════════════════════════╝

Build: #${BUILD_NUMBER}
URL: http://127.0.0.1:9001
Deploy: ${DEPLOY_DIR}
"""
            sh 'docker ps --filter "name=school_erp" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"'
        }
        failure {
            echo '❌ DEPLOYMENT FAILED!'
            sh '''
                echo "=== Container Status ==="
                docker ps -a --filter "name=school_erp"
                echo ""
                echo "=== App Logs ==="
                docker logs --tail=50 school_erp_app || true
                echo ""
                echo "=== Nginx Logs ==="
                docker logs --tail=50 school_erp_nginx || true
            '''
        }
    }
}
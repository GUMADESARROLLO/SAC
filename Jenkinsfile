pipeline {
    agent any
    environment {
		DOCKERHUB_CREDENTIALS=credentials('DockerHub')
	}
    stages {
        stage('Docker Build') {
            steps {
                sh "docker build -t gumadesarrollo/sac:v1.2.${env.BUILD_NUMBER} ."
            }
        }
        stage('Docker Push') {
            steps {
                sh 'echo $DOCKERHUB_CREDENTIALS_PSW | docker login -u $DOCKERHUB_CREDENTIALS_USR --password-stdin'
                sh "docker push gumadesarrollo/sac:v1.2.${env.BUILD_NUMBER}"
            }
        }
        stage('Docker Remove Image') {
            steps {
                sh "docker rmi gumadesarrollo/sac:v1.2.${env.BUILD_NUMBER}"
            }
        }
        stage('Apply Kubernetes Files'){
            steps {
                sh 'cat deployment.yaml | sed "s/v1.2.{{BUILD_NUMBER}}/v1.2.$BUILD_NUMBER/g" | kubectl apply -f -'
                sh 'kubectl apply -f service.yaml'
                
            }
        }
    }
}
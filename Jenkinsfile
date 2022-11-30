pipeline {
    agent any
    environment {
		DOCKERHUB_CREDENTIALS=credentials('DockerHub')
	}
    stages {
        stage('Docker Build') {
            steps {
                sh "docker build -t gumadesarrollo/sac:${env.BUILD_NUMBER} ."
            }
        }
        stage('Docker Push') {
            steps {
                sh 'echo $DOCKERHUB_CREDENTIALS_PSW | docker login -u $DOCKERHUB_CREDENTIALS_USR --password-stdin'
                sh "docker push gumadesarrollo/sac:${env.BUILD_NUMBER}"
            }
        }
        stage('Docker Remove Image') {
            steps {
                sh "docker rmi gumadesarrollo/sac:${env.BUILD_NUMBER}"
            }
        }
    }
}
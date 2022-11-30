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
        stage('Apply Kubernetes Files'){
            steps {
                withKubeConfig(caCertificate: '', clusterName: 'microk8s-cluster', contextName: '', credentialsId: 'Microk8sServer', namespace: 'kube-system', serverUrl: ' https://192.168.1.40:16443') {
                    sh 'cat deployment.yaml | sed "s/{{BUILD_NUMBER}}/$BUILD_NUMBER/g" | kubectl apply -f -'
                    sh 'kubectl apply -f service.yaml'
                }
                
            }
        }
    }
}
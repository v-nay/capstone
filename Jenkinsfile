pipeline {
  agent any
  tools {nodejs "nodejs-16"}
  environment {
    BRANCH = 'ver-8'
    scannerHome = tool 'SonarQubeScan';
  }
  stages {
        stage('get_commit_msg') {
          agent any
           when {
             anyOf {
                branch 'ver-8';
                changeRequest target: 'ver-8';
             }
            }
          steps {
              script {
                notifyStarted()
                passedBuilds = []
                lastSuccessfulBuild(passedBuilds, currentBuild);
                env.changeLog = getChangeLog(passedBuilds)
                echo "changeLog \n${env.changeLog}"
              }
          }
        }
        stage('Analysis & Deploy') {
          parallel{
            stage('SonarQube Analysis') {
              steps {
                withSonarQubeEnv(installationName: 'SonarQubePro') {
                sh "${scannerHome}/bin/sonar-scanner"
                }
              }
            }

        stage('Dev Build') {
        agent any
        when {
                branch 'ver-8'
            }
        steps {
          script {
            sshagent(['72c3455a-de8d-4b39-9f02-771ddb2fdf00']) {
            sh '''
            ssh -tt -o StrictHostKeyChecking=no root@159.89.161.57 -p 3030 << EOF
            cd /var/www/ekcms/; \
            git pull origin ver-8; \
            composer install; \
            composer clear-cache; \
            composer dump-autoload; \
            php artisan cache:clear; \
            php artisan config:clear; \
            php artisan migrate --force; \
            npm install; \
            npm run dev; \
            exit
            EOF '''
            }
          }
          }
        }

  }
        }
  }


  post{
      success{
        script {
          if (env.BRANCH_NAME == 'ver-8' || env.BRANCH_NAME == 'qa' || env.BRANCH_NAME == 'uat' || env.BRANCH_NAME == 'live')
            notifySuccessful()
        }
      }
      failure{
        notifyFailed()
      }
  }
}
// def notifyStarted() {
// mattermostSend (
//   color: "#2A42EE",
//   channel: 'ekcms-ver7-ver8',
//   endpoint: 'https://ekbana.letsperk.com/hooks/f8mxssqga7rn983duwfrg1hxze',
//   message: "Build STARTED: ${env.JOB_NAME} #${env.BUILD_NUMBER} (<${env.BUILD_URL}|Link to build>)"
//   )
// }

// def notifySuccessful() {
// mattermostSend (
//   color: "#00f514",
//   channel: 'ekcms-ver7-ver8',
//   endpoint: 'https://ekbana.letsperk.com/hooks/f8mxssqga7rn983duwfrg1hxze',
//   message: "Build SUCCESS: ${env.JOB_NAME} #${env.BUILD_NUMBER} (<${env.BUILD_URL}|Link to build>):\n${changeLog}"
//   )
// }

// def notifyFailed() {
// mattermostSend (
//   color: "#e00707",
//   channel: 'ekcms-ver7-ver8',
//   endpoint: 'https://ekbana.letsperk.com/hooks/f8mxssqga7rn983duwfrg1hxze',
//   message: "Build FAILED: ${env.JOB_NAME} #${env.BUILD_NUMBER} (<${env.BUILD_URL}|Link to build>)"
//   )
// }
// def lastSuccessfulBuild(passedBuilds, build) {
//   if ((build != null) && (build.result != 'SUCCESS')) {
//       passedBuilds.add(build)
//       lastSuccessfulBuild(passedBuilds, build.getPreviousBuild())
//    }
// }

@NonCPS
def getChangeLog(passedBuilds) {
    def log = ""
    for (int x = 0; x < passedBuilds.size(); x++) {
        def currentBuild = passedBuilds[x];
        def changeLogSets = currentBuild.changeSets
        for (int i = 0; i < changeLogSets.size(); i++) {
            def entries = changeLogSets[i].items
            for (int j = 0; j < entries.length; j++) {
                def entry = entries[j]
                log += "* ${entry.msg} by ${entry.author} \n"
            }
        }
    }
    return log;
}

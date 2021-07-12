<?php

/**
 * Class Issues
 */
class Issues extends Controller
{
  // environment variables
    private $env;

    function __construct() {
      $path = str_replace("application\controller", "", __DIR__).".env";

      $envFile = fopen($path, "r") or die("Unable to open file!");
      $this->env = json_decode(fread($envFile,filesize($path)));
      fclose($envFile);
    }
    /**
     * Get issues
     */
    public function get()
    {
        $accessToken = $this->env->accessToken;

        // First time using graphql, it is very cool
        $endpoint = "https://api.github.com/graphql";
        $page_size = $_GET["page_size"];
        $cursor = isset($_GET["cursor"]) ? $_GET["cursor"] : null;
        $owner = $this->env->owner;
        $repo_name = $this->env->repoName;
        $paging = $cursor == null ? "" : ", before: \"".$cursor."\"";

        $query = "query {
          repository(owner: \"".$owner."\", name: \"".$repo_name."\") {
            issues(last: ".$page_size.$paging.") {
              edges {
                node {
                  title
                  labels(first: 20) {
                    edges {
                      node {
                        name
                        description
                      }
                    }
                  }
                  assignees(first: 10) {
                    edges {
                      node {
                        name
                      }
                    }
                  }
                  body
                  closed
                }
                cursor
              }
            }
          }
        }";
        $variables = '';
        
        $json = json_encode(['query' => $query, 'variables' => $variables]);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        
        $headers = array();
        $headers[] = 'Authorization: bearer '.$accessToken;
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        $headers[] = 'User-Agent: php';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        if ($result === FALSE) {
          throw new Exception('Error fetching issues.');
        }

        echo $result;
    }

    /**
     * Create Issue
     */
    public function add()
    {
        if (!isset($_POST["title"])) {
          throw new Exception('Missing field title.');
        }
        if (!isset($_POST["description"])) {
          throw new Exception('Missing field description.');
        }
        if (!isset($_POST["client"])) {
          throw new Exception('Missing field client.');
        }
        if (!isset($_POST["priority"])) {
          throw new Exception('Missing field priority.');
        }
        if (!isset($_POST["type"])) {
          throw new Exception('Missing field type.');
        }

        $accessToken = "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";

        $endpoint = "https://api.github.com/graphql";
        $owner = "KwasiKK";
        $repo_name = "loola_api";


        $query = "{\"title\":\"".$_POST["title"]."\", \"body\":\"".$_POST["description"]."\", ".
          "\"labels\": [\"C: ".$_POST["client"]."\", \"P: ".$_POST["priority"]."\", \"T: ".$_POST["type"]."\"]".
        "}";

        $post = [
          'title' => $_POST["title"],
          'body' => $_POST["description"]
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.github.com/repos/'.$owner.'/'.$repo_name.'/issues');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        
        $headers = array();
        $headers[] = 'Authorization: bearer '.$accessToken;
        $headers[] = 'Accept: application/vnd.github.v3+json';
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        $headers[] = 'User-Agent: php';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        if ($result === FALSE) {
          throw new Exception('Error creating issue.');
        }

        echo $result;
    }
}

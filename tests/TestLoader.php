<?php 
 require_once __DIR__.'/../vendor/autoload.php';

class TestLoader extends \PHPUnit_Extensions_Selenium2TestCase
{

    protected $Baseurl = 'http://herman.berrybenka.biz';
    protected $env = 'local';
 

    protected function setUp() {
        error_reporting(0);
        if($this->env == 'local')
        {            
            $this->setBrowser('firefox');
            $this->setBrowserUrl($this->Baseurl);
            $this->run_selenium_server();
            $this->run_phantom_js();
        }
    }

    protected function run_selenium_server()
    {
        if($this->selenium_server_already_running())
        {
            fwrite(STDOUT, "Selenium server already running\n");
        }
        else
        {
            shell_exec("java -jar " . __DIR__ . "\bin\selenium-server-standalone-2.53.0.jar");
        }
    }

    protected function run_phantom_js()
    {
        if ($this->phantom_js_already_running()) {
            fwrite(STDOUT, "PhantomJS already running\n");
        } else {
            fwrite(STDOUT, "Starting PhantomJS\n");
            shell_exec(__DIR__ . "\bin\phantomjs.exe --webdriver=8080 --webdriver-selenium-grid-hub=http://127.0.0.1:4444");
        }
    }

    protected function selenium_server_already_running()
    {
        return fsockopen("localhost", 4444);
    }

    protected function phantom_js_already_running()
    {
        try {
            return fsockopen("localhost", 8080);
        } catch (Exception $e) {
        }
    }

    public function setUpPage()
    {
         //$this->currentWindow()->maximize();
    }

     protected function takeScreenShot($location){
        $fp = fopen($location,'wb');
        fwrite($fp,$this->currentScreenshot());
        fclose($fp);
    }

    protected function see($name)
    {
        return $this->byXpath("//*[contains(text(),'".$name."')]");
    }

    protected function click($name)
    {
        $element = $this->byXpath("//*[contains(text(),'".$name."')]");
        $element->click();
    }

    protected function seePageIs($name)
    {
        $this->assertEquals($this->Baseurl.$name, $this->url()); 
    }

    protected function clickLink($link)
    {
        $element = $this->byXpath("//a[@href='".$link."']");
        $element->click();
    }

    protected function visit($path)
    {
        $this->url($path);
    }

    protected function clickCustomSelectWith($attribute,$name,$value)
    {
      $script =  '$(\'select['.$attribute.'="'.$name.'"]\').next().find(\'ul\').find(\'input:radio[value="'.$value.'"]\').trigger(\'click\');';

       $this->execute(array(
            'script' => $script,
            'args'   => array()
        ));
    }
}
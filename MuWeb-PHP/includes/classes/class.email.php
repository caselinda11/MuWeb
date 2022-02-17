<?php
/**
 * 文件说明
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

class Email {
	
	private $_active = false;
	private $_smtp = false;
	
	private $_from;
	private $_name;
	private $_templates = array();
	private $_templatesPath = __PATH_INCLUDES_EMAILS__;
	
	private $_smtpHost;
	private $_smtpPort;
	private $_smtpUser;
	private $_smtpPass;
	
	private $_template;
	private $_message;
	private $_to = [];
	private $_subject;
	private $_variables = [];
	private $_values = [];
	private $mail;
	private $_isCustomTemplate=false;

    /**
     * Email constructor.
     * @throws Exception
     */
	function __construct() {
		# load configs
		$configs = gconfig('email',true);
		if(!is_array($configs)) throw new Exception('[邮件系统] 无法加载邮箱配置。');
		
		# set configurations
		$this->_active = $configs['active'];
		$this->_smtp = $configs['smtp_active'];
		$this->_from = $configs['send_from'];
		$this->_name = $configs['send_name'];
		$this->_smtpHost = $configs['smtp_host'];
		$this->_smtpPort = $configs['smtp_port'];
		$this->_smtpUser = $configs['smtp_user'];
		$this->_smtpPass = $configs['smtp_pass'];
		
		# check if templates exist
		if(!is_array($configs['email_templates']['template'])) throw new Exception();
		
		# load templates list
		$templates = array();
		foreach($configs['email_templates']['template'] as $template) {
			$templates[$template['filename']] = str_replace("{SERVER_NAME}", config('server_name'), $template['subject']);
		}
		
		# server name variable
		$this->addVariable("{SERVER_NAME}", config('server_name'));
		
		# save templates
		$this->_templates = $templates;
		
		# phpmailer instance
		$this->mail = new PHPMailer\PHPMailer\PHPMailer(true);
		
	}

    /**
     * @param $subject
     */
	public function setSubject($subject) {
		$this->_subject = $subject;
	}

    /**
     * @param $email
     * @param string $name
     */
	public function setFrom($email, $name="Unknown") {
		$this->_from = $email;
		$this->_name = $name;
	}

    /**
     * @param $message
     */
	public function setMessage($message) {
		$this->_message = $message;
	}

    /**
     * @param $template
     * @throws Exception
     */
	public function setTemplate($template) {
		if(!array_key_exists($template, $this->_templates)) throw new Exception('[邮件系统] 无法加载邮箱模板。');
		$this->_template = $template;
		$this->_subject = $this->_templates[$template];
	}

    /**
     * @param $variable
     * @param $value
     */
	public function addVariable($variable, $value) {
		$this->_variables[] = $variable;
		$this->_values[] = $value;
	}

    /**
     * @param $email
     * @throws Exception
     */
	public function addAddress($email) {
		if(!Validator::Email($email)) throw new Exception('[邮件系统] 邮箱地址无效，无法发送邮箱。');
		$this->_to[] = $email;
	}

    /**
     * @return false|string
     * @throws Exception
     */
	private function _loadTemplate() {
		if(!$this->_template) throw new Exception('[邮件系统] 您没有设置模板。');
		
		// custom template
		if($this->_isCustomTemplate) {
			if(!file_exists($this->_template)) throw new Exception('[邮件系统] 无法加载自定义邮箱模板（您必须提供模板的完整路径）。');
			return file_get_contents($this->_template);
		}
		
		if(!file_exists($this->_templatesPath . $this->_template . '.txt')) throw new Exception('[邮件系统] 无法加载邮箱模板。');
		return file_get_contents($this->_templatesPath . $this->_template . '.txt');
	}

    /**
     * @return string|string[]
     * @throws Exception
     */
	private function _prepareTemplate() {
		return str_replace($this->_variables, $this->_values, $this->_loadTemplate());
	}

    /**
     * @return bool
     * @throws Exception
     */
	public function send() {
		if(!$this->_active) throw new Exception('由于邮箱系统未处于活动状态，您的请求无法处理，请与在线客服联系。');
		
		if(!$this->_message) {
			if(!$this->_template) throw new Exception('[邮件系统] 尚未设置邮箱模板。');
		}
		
		if(!is_array($this->_to)) throw new Exception('[邮件系统] 没有设置收件人邮箱地址。');
		
		if($this->_smtp) {
			$this->mail->IsSMTP();
			$this->mail->SMTPAuth = true;
			$this->mail->Host = $this->_smtpHost;
			$this->mail->Port = $this->_smtpPort;
			$this->mail->Username = $this->_smtpUser;
			$this->mail->Password = $this->_smtpPass;
			$this->mail->CharSet = "utf-8";
		}
		
		$this->mail->SetFrom($this->_from, $this->_name);
		
		foreach($this->_to as $address) {
			$this->mail->AddAddress($address);
		}
		
		if(!$this->_subject) throw new Exception('[邮件系统] 邮箱主题尚未设置。');
		$this->mail->Subject = $this->_subject;
		
		if(!$this->_message) {
			$this->mail->MsgHTML($this->_prepareTemplate());
		} else {
			$this->mail->MsgHTML($this->_message);
		}
		
		if($this->mail->Send()) return true;
		return false;
	}

    /**
     * @param $template
     */
	public function setCustomTemplate($template) {
		$this->_template = $template;
		$this->_isCustomTemplate = true;
	}
	
}
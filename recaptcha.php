<?php

class recaptcha extends rcube_plugin
{
    public function init()
    {
        $this->load_config();
	$this->include_script('recaptcha.js');
        $rcmail = rcmail::get_instance();
        if ($rcmail->config->get('recaptcha_public_key') != '' && $rcmail->config->get('recaptcha_secret_key') != '') {
            $this->add_hook('template_object_loginform', [$this, 'template_object_loginform']);
            $this->add_hook('authenticate',              [$this, 'authenticate']);
        }
    }

    public function template_object_loginform(array $loginform) : array
    {
        $rcmail = rcmail::get_instance();
        $key    = $rcmail->config->get('recaptcha_public_key');
        $theme  = $rcmail->config->get('recaptcha_theme') ?? 'dark';

        $src    = "https://www.google.com/recaptcha/api.js?hl=" . urlencode($rcmail->user->language);
        $script = html::tag('script', ['type' => "text/javascript", 'src' => $src]);
        $this->include_script($src);

        $loginform['content'] = str_ireplace(
            '</tbody>',
            '<tr><td class="title"></td><td class="input"><div class="g-recaptcha" id="recaptcha-checkbox" data-theme="' . html::quote($theme) . '" data-sitekey="' . html::quote($key) . '" data-callback="recaptchaCallback"></div></td></tr></tbody>',
            $loginform['content']
        );

	if($rcmail->config->get('auto_disable')) {
		$loginform['content'] = str_ireplace(
		    '<input type="submit" id="rcmloginsubmit" class="button mainaction" value="Login">',
		    '<input type="submit" id="rcmloginsubmit" class="button mainaction" value="Login" disabled>',
		    $loginform['content']
		);
	}

        return $loginform;
    }

    public function authenticate(array $args)
    {
        $rcmail    = rcmail::get_instance();
        $secret    = $rcmail->config->get('recaptcha_secret_key');
        $recaptcha = new \ReCaptcha\ReCaptcha($secret);
        $cf        = new \CloudFlare\IpRewrite();

        $response  = filter_input(INPUT_POST, 'g-recaptcha-response');
        $ip        = $cf->isCloudFlare() ? $cf->getRewrittenIP() : rcube_utils::remote_addr();
        $result    = $recaptcha->verify($response, $ip);

        if ($result->isSuccess()) {
            return $args;
        }

        $this->add_texts('localization/');
        $rcmail->output->show_message('recaptcha.recaptchafailed', 'error');
        $rcmail->output->set_env('task', 'login');
        $rcmail->output->send('login');
        return null;
    }
}

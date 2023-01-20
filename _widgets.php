<?php
/**
 * @brief Clock, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Moe, Pierre Van Glabeke and contributors
 *
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
if (!defined('DC_RC_PATH')) { return; }

dcCore::app()->addBehavior('initWidgets',array('ClockBehaviors','initWidgets'));

class ClockBehaviors
{
	public static function initWidgets($w)
	{
		# set timezone
		$tz = dcCore::app()->blog->settings->system->blog_timezone;

		$w->create('Clock',__('Clock'),array('publicClock','Show'),
			null,
			__('Display the date of a time zone'));

		$w->Clock->setting('title',__('Title:'),
			sprintf(__('Local time in %s'),substr(strrchr($tz,'/'),1)),
			'text');

		$w->Clock->setting('timezone',__('Timezone:'),$tz,'combo',
			dt::getZones(true,false));

		$w->Clock->setting('format',
			sprintf(__('Format (see <a href="%1$s" %2$s>PHP strftime function</a>) (HMS display dynamically %%H:%%M:%%S)<br />(on Microsoft Windows servers, you have to use %%d instead of %%e):'),
			__('http://www.php.net/manual/en/function.strftime.php'),
			'onclick="return window.confirm(\''.__('Are you sure you want to leave this page?').'\')"'),
			'%A, %e %B %Y, HMS','text');

		$w->Clock->setting('homeonly',__('Display on:'),0,'combo',
			array(
				__('All pages') => 0,
				__('Home page only') => 1,
				__('Except on home page') => 2
				)
		);
    $w->Clock->setting('content_only',__('Content only'),0,'check');
    $w->Clock->setting('class',__('CSS class:'),'');
		$w->Clock->setting('offline',__('Offline'),0,'check');
	}
}

class publicClock
{
	public static function Show($w)
	{
		if ($w->offline)
			return;

		if (($w->homeonly == 1 && dcCore::app()->url->type != 'default') ||
			($w->homeonly == 2 && dcCore::app()->url->type == 'default')) {
			return;
		}

		# output
		$header = (strlen($w->title) > 0) ? '<h2>'.html::escapeHTML($w->title).'</h2>' : null;

		if (strpos($w->format, 'HMS') !== False)
		{
			$id = str_replace('/','',strtolower($w->timezone));

			$js = (string)'';
			$js .= '<script>';
			/* http://binnyva.blogspot.com/2005/12/my-custom-javascript-functions.html */
			if (!defined('CLOCK_GEBI'))
			{
				$js .= 'function gEBI(id) {return document.getElementById(id);}';
				$js .= 'function zeros(int) {if (10 > int) {int = \'0\'+int;}return int;}';
				$js .= 'var d = new Date();';
				define('CLOCK_GEBI',(bool)true);
			}

			$js .= 'var diffH_'.$id.' = (d.getHours()-'.(dt::str('%H',null,$w->timezone)*1).');';

			$js .= 'function clock_'.$id.'() {'.
				'var d = new Date();'.
				'var h = zeros(d.getHours()-diffH_'.$id.');'.
				'var m = zeros(d.getMinutes());'.
				'var s = zeros(d.getSeconds());'.
				'gEBI(\'hms_'.$id.'\').innerHTML = h+\':\'+m+\':\'+s;'.
				'setTimeout("clock_'.$id.'()",500);'.
				'}';

			$js .= 'clock_'.$id.'();';
			$js .= '</script>';

			$hms = '<span id="hms_'.$id.'">'.dt::str('%H',null,$w->timezone).':'.dt::str('%M',null,$w->timezone).':'.dt::str('%S',null,$w->timezone).'</span>';
			$time = dt::str($w->format,null,$w->timezone);
			$time = str_replace('HMS',$hms,$time);
		}
		else
		{
			$time = dt::str($w->format,null,$w->timezone);
			$js = null;
		}

		$res =
		($w->title ? $w->renderTitle(html::escapeHTML($w->title)) : '').
    '<p class="text">'.$time.'</p>'.$js;

		return $w->renderDiv($w->content_only,'clock '.$w->class,'',$res);
	}
}
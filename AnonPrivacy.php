<?php

use Wikimedia\IPUtils;

class AnonPrivacy {

	/**
	 * @link https://www.mediawiki.org/wiki/Manual:Hooks/HtmlPageLinkRendererEnd
	 * @param LinkRenderer $linkRenderer
	 * @param LinkTarget $target
	 * @param bool $isKnown
	 * @param string &$text
	 * @param string[] &$attribs
	 * @param string &$ret
	 * @return bool
	 */
	public static function onHtmlPageLinkRendererEnd( $linkRenderer, $target, $isKnown, &$text, &$attribs, &$ret ) {
		// MW_NO_SESSION happens if we are called from load.php, in which
		// case we can not check user permissions, so fail safe. This is known
		// to happen with VisualEditor.
		if (
			!defined( 'MW_NO_SESSION' ) &&
			RequestContext::getMain()->getUser()->isAllowed( 'anonprivacy' )
		) {
			return true;
		}

		if ( array_key_exists( 'class', $attribs ) ) {
			$class = $attribs['class'];
		} else {
			return true;
		}

		// Check if we're dealing with a link to an anonymous user page
		if ( strpos( $class, 'mw-anonuserlink' ) > -1 ) {
			$privacypage = wfMessage( 'privacypage' )->plain();
			$privacytitle = Title::newFromText( $privacypage );
			$attribs['href'] = $privacytitle->getInternalURL();
			$attribs['class'] = 'mw-userlink mw-anonuserlink';
			$attribs['title'] = wfMessage( 'privacy' )->text();
			$text = wfMessage( 'anonprivacy-anon' )->text();
		}

		// Check if we're dealing with a link to an anonymous user talk page
		if ( strpos( $class, 'mw-usertoollinks' ) > -1 && $target->getNamespace() === NS_USER_TALK && IPUtils::isIPAddress( $target->getText() ) ) {
			$privacypage = wfMessage( 'privacypage' )->plain();
			$privacytitle = Title::newFromText( $privacypage );
			$attribs['href'] = $privacytitle->getInternalURL();
			$attribs['class'] = 'mw-usertoollinks-talk';
			$attribs['title'] = wfMessage( 'privacy' )->text();
			$text = wfMessage( 'privacy' )->text();
		}
	}
}

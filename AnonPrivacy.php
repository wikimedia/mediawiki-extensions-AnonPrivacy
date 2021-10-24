<?php

use MediaWiki\MediaWikiServices;
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
		if ( in_array(
			'anonprivacy',
			MediaWikiServices::getInstance()->getPermissionManager()
				->getUserPermissions( RequestContext::getMain()->getUser() )
		) ) {
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
			$attribs['title'] = wfMessage( 'privacy' );
			$text = wfMessage( 'anonprivacy-anon' );
		}

		// Check if we're dealing with a link to an anonymous user talk page
		if ( strpos( $class, 'mw-usertoollinks' ) > -1 && $target->getNamespace() === NS_USER_TALK && IPUtils::isIPAddress( $target->getText() ) ) {
			$privacypage = wfMessage( 'privacypage' )->plain();
			$privacytitle = Title::newFromText( $privacypage );
			$attribs['href'] = $privacytitle->getInternalURL();
			$attribs['class'] = 'mw-usertoollinks-talk';
			$attribs['title'] = wfMessage( 'privacy' );
			$text = wfMessage( 'privacy' );
		}
	}
}

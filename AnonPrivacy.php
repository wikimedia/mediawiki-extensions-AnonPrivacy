<?php

class AnonPrivacy {

	public static function onHtmlPageLinkRendererEnd( $linkRenderer, $target, $isKnown, &$text, &$attribs, &$ret ) {
		if ( in_array(
			'anonprivacy',
			RequestContext::getMain()->getUser()->getRights()
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
		if ( strpos( $class, 'mw-usertoollinks' ) > -1 && $target->getNamespace() === NS_USER_TALK && IP::isIPAddress( $target->getText() ) ) {
			$privacypage = wfMessage( 'privacypage' )->plain();
			$privacytitle = Title::newFromText( $privacypage );
			$attribs['href'] = $privacytitle->getInternalURL();
			$attribs['class'] = 'mw-usertoollinks-talk';
			$attribs['title'] = wfMessage( 'privacy' );
			$text = wfMessage( 'privacy' );
		}
	}
}

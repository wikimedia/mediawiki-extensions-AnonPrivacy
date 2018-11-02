<?php

class AnonPrivacy {

	public static function onHtmlPageLinkRendererEnd( $linkRenderer, $target, $isKnown, &$text, &$attribs, &$ret ) {

		global $wgUser;
		if ( in_array( 'anonprivacy', $wgUser->getRights() ) ) {
			return true;
		}

		if ( array_key_exists( 'class', $attribs ) ) {
			$class = $attribs['class'];
		} else {
			return true;
		}

		// Check if we're dealing with a link to an anonymous user page
		if ( strpos( $class, 'mw-anonuserlink' ) > -1 ) {
			$ret = wfMessage( 'anonprivacy-anon' );
			return false;
		}

		// Check if we're dealing with a link to an anonymous user talk page
		if ( strpos( $class, 'mw-usertoollinks' ) > -1 && $target->getNamespace() === NS_USER_TALK && IP::isIPAddress( $target->getText() ) ) {
			$privacypage = wfMessage( 'privacypage' )->plain();
			$privacytitle = Title::newFromText( $privacypage );
			$attribs = [
				'href' => $privacytitle->getInternalURL()
			];
			$text = wfMessage( 'privacy' );
		}
	}
}
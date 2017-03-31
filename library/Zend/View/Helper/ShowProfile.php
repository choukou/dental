<?php

class Zend_View_Helper_ShowProfile extends Zend_View_Helper_Abstract {

		public function showProfile($rank = 0, $sex = 1) {
			$img = '/assets/images/ico_cap/v_default.png';
			switch ($rank) {
				case 0:
					if($sex == 1) {
						$img = '/assets/images/ico_cap/v0.png';
					} else {
						$img = 'assets/images/ico_cap/mv0.png';
					}
					break;
				case 1:
					if($sex == 1) {
						$img = '/assets/images/ico_cap/v1.png';
					} else {
						$img = '/assets/images/ico_cap/mv1.png';
					}
					break;
				case 2:
					if($sex == 1) {
						$img = '/assets/images/ico_cap/v2.png';
					} else {
						$img = '/assets/images/ico_cap/mv2.png';
					}
					break;
				case 3:
					if($sex == 1) {
						$img = '/assets/images/ico_cap/v3.png';
					} else {
						$img = '/assets/images/ico_cap/mv3.png';
					}
					break;
				case 4:
					if($sex == 1) {
						$img = '/assets/images/ico_cap/v4.png';
					} else {
						$img = '/assets/images/ico_cap/mv4.png';
					}
					break;
				case 5:
					if($sex == 1) {
						$img = '/assets/images/ico_cap/v5.png';
					} else {
						$img = '/assets/images/ico_cap/mv5.png';
					}
					break;

				default:
					$img = '/assets/images/ico_cap/v_default.png';
					break;
			}
			return $img;
		}
}

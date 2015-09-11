<?php
/**
 * Link to the year
 *
 * @author  Tim Lochmüller
 */

namespace HDNET\Calendarize\ViewHelpers\Link;

/**
 * Link to the year
 *
 * @author Tim Lochmüller
 */
class YearViewHelper extends AbstractLinkViewHelper
{

    /**
     * Render the link to the given day
     *
     * @param \DateTime $date
     * @param int       $pageUid
     *
     * @return string
     */
    public function render(\DateTime $date, $pageUid = null)
    {
        $additionalParams = array(
            'tx_calendarize_calendar' => array(
                'year' => $date->format('Y'),
            ),
        );
        return parent::render($this->getPageUid($pageUid), $additionalParams);
    }
}
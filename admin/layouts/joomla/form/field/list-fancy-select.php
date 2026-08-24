<?php // phpcs:ignore PSR1.Files.SideEffects.FoundWithSymbols

/**
 * @package     HwdLinks.Administrator
 * @subpackage  com_hwdlinks
 *
 * Component override of Joomla's core joomla.form.field.list-fancy-select
 * layout. It is otherwise identical to Joomla's own layout, but also
 * recognises a couple of extra $displayData keys ($remoteSearch /
 * $remoteUrl) so fields can opt into Choices.js "remote-search" mode.
 *
 * That's needed because Choices.js's default client-side search (Fuse.js,
 * using the Bitap algorithm) cannot match search terms longer than ~32
 * characters. Remote search runs the search as a normal server-side SQL
 * query instead, which has no such limit.
 *
 * @copyright   Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

extract($displayData);

/**
 * Layout variables
 * -----------------
 * @var   string   $autocomplete    Autocomplete attribute for the field.
 * @var   boolean  $autofocus       Is autofocus enabled?
 * @var   string   $class           Classes for the input.
 * @var   string   $description     Description of the field.
 * @var   boolean  $disabled        Is this field disabled?
 * @var   string   $group           Group the field belongs to. <fields> section in form XML.
 * @var   boolean  $hidden          Is this field hidden in the form?
 * @var   string   $hint            Placeholder for the field.
 * @var   string   $id              DOM id of the field.
 * @var   string   $label           Label of the field.
 * @var   string   $labelclass      Classes to apply to the label.
 * @var   boolean  $multiple        Does this field support multiple values?
 * @var   string   $name            Name of the input field.
 * @var   string   $onchange        Onchange attribute for the field.
 * @var   string   $onclick         Onclick attribute for the field.
 * @var   string   $pattern         Pattern (Reg Ex) of value of the form field.
 * @var   boolean  $readonly        Is this field read only?
 * @var   boolean  $repeat          Allows extensions to duplicate elements.
 * @var   boolean  $required        Is this field required?
 * @var   integer  $size            Size attribute of the input.
 * @var   boolean  $spellcheck      Spellcheck state for the form field.
 * @var   string   $validate        Validation rules to apply.
 * @var   string   $value           Value attribute of the field.
 * @var   array    $options         Options available for this field.
 * @var   string   $dataAttribute   Miscellaneous data attributes preprocessed for HTML output
 * @var   array    $dataAttributes  Miscellaneous data attribute for eg, data-*
 *
 * @var   boolean  $remoteSearch    (HwdLinks addition) Enable Choices.js remote-search mode.
 * @var   string   $remoteUrl       (HwdLinks addition) Url to query for remote search results.
 * @var   string   $remoteTermKey   (HwdLinks addition) Query string key for the search term. Default "term".
 * @var   integer  $remoteMinChars  (HwdLinks addition) Minimum characters before a remote search fires. Default 1.
 */

$html = [];
$attr = '';

// Initialize the field attributes.
$attr .= !empty($size) ? ' size="' . $size . '"' : '';
$attr .= $multiple ? ' multiple' : '';
$attr .= $autofocus ? ' autofocus' : '';
$attr .= $onchange ? ' onchange="' . $onchange . '"' : '';
$attr .= $dataAttribute;

// To avoid user's confusion, readonly="readonly" should imply disabled="disabled".
if ($readonly || $disabled) {
    $attr .= ' disabled="disabled"';
}

$attr2  = '';
$attr2 .= !empty($class) ? ' class="' . $class . '"' : '';
$attr2 .= ' placeholder="' . $this->escape($hint ?: Text::_('JGLOBAL_TYPE_OR_SELECT_SOME_OPTIONS')) . '" ';

if ($required) {
    $attr  .= ' required class="required"';
    $attr2 .= ' required';
}

// HwdLinks addition: wire up Choices.js remote-search mode.
if (!empty($remoteSearch) && !empty($remoteUrl)) {
    $attr2 .= ' remote-search url="' . htmlspecialchars($remoteUrl, ENT_COMPAT, 'UTF-8') . '"';
    $attr2 .= ' term-key="' . htmlspecialchars($remoteTermKey ?? 'term', ENT_COMPAT, 'UTF-8') . '"';
    $attr2 .= ' min-term-length="' . (int) ($remoteMinChars ?? 1) . '"';
}

// Create a read-only list (no name) with hidden input(s) to store the value(s).
if ($readonly) {
    $html[] = HTMLHelper::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $value, $id);

    // E.g. form field type tag sends $this->value as array
    if ($multiple && is_array($value)) {
        if (!count($value)) {
            $value[] = '';
        }

        foreach ($value as $val) {
            $html[] = '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($val, ENT_COMPAT, 'UTF-8') . '">';
        }
    } else {
        $html[] = '<input type="hidden" id="' . $id . '-value" name="' . $name . '" value="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '">';
    }
} else { // Create a regular list.
    $html[] = HTMLHelper::_('select.genericlist', $options, $name, trim($attr), 'value', 'text', $value, $id);
}

Text::script('JGLOBAL_SELECT_NO_RESULTS_MATCH');
Text::script('JGLOBAL_SELECT_PRESS_TO_SELECT');

Factory::getApplication()->getDocument()->getWebAssetManager()
    ->usePreset('choicesjs')
    ->useScript('webcomponent.field-fancy-select');

// HwdLinks addition: patch Choices.js's long-search-pattern fallback.
//
// Fuse.js (bundled by Choices.js) can only fuzzy-match patterns up to 32
// characters (a hard limit of the Bitap algorithm it uses). Past that it
// silently switches to matching "any single word from the query, anywhere
// in the label" - which for common words ("de", "la", ...) matches almost
// everything and buries genuine results.
//
// Fields opting in via data-strict-search="1" get that fallback patched to
// require the whole typed phrase as one contiguous substring instead -
// consistent with the server-side SQL LIKE search used for remote-search.
//
// The patch is applied to the live Choices instance (not by intercepting
// the `Choices` constructor) so it works no matter the order scripts load
// in: `config.fuseOptions` is read fresh on every search, so patching it
// any time before the user actually searches - here, on focus - is safe.
if (!empty($remoteSearch)) {
    Factory::getApplication()->getDocument()->addScriptDeclaration(
        <<<'JS'
        (function () {
            if (window.hwdlinksStrictSearchBound) {
                return;
            }
            window.hwdlinksStrictSearchBound = true;

            document.addEventListener('focusin', function (event) {
                var wrapper = event.target.closest && event.target.closest('joomla-field-fancy-select');

                if (!wrapper || !wrapper.choicesInstance) {
                    return;
                }

                var select = wrapper.querySelector('select[data-strict-search]');
                var instance = wrapper.choicesInstance;

                if (!select || !instance.config || (instance.config.fuseOptions && instance.config.fuseOptions.tokenSeparator)) {
                    return;
                }

                instance.config.fuseOptions = Object.assign({}, instance.config.fuseOptions, {
                    // A regex that can never match, so String.replace() leaves the
                    // pattern's spaces untouched instead of OR-ing the words together.
                    tokenSeparator: /(?!x)x/g,
                });
            });
        })();
        JS
    );
}

?>

<joomla-field-fancy-select <?php echo $attr2; ?>><?php echo implode($html); ?></joomla-field-fancy-select>

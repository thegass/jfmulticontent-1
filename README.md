# TYPO3 extension jfmulticontent

I have developed the existing jfmulticontent extension for the TYPO3 version 8.7. TYPO3 9 is working.

## Flexform Migration

The upgrade script must be executed in the Install Tool or in the Extension Manager once. All the flexform sheet names must have a leading 's_'. This transformation is done in the update script. Older versions of this extension require some more modifications, which the Extension Manager update script will perform.

The extension t3jquery seems not to exist any more and TYPO3 10 will provide jQuery for extensions. Any support for t3jquery will therefore be dropped soon.


Any contributions are welcome. Just create an issue or write a pull request.

TYPO3 8 and later require the extension patchlayout to be installed.

## TSConfig Requirement

In TYPO3 > 7.4 you must set TSConfig like this:
### example:
```
TCEFORM.tt_content.tx_jfmulticontent_contents.PAGE_TSCONFIG_ID = 17

```

The starting point page record in the plugin is not used any more.



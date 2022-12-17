# No Copy/Paste

This REDCap External Module allows project designers to prevent users or survey respondents from copying from, or pasting into, text entry fields.

## Installation

Install the module from the REDCap module repository and enable in the Control Center, then enable on projects as needed.

## Usage

This module adds six action tags:

- @NOCOPY – Prevents the user from copying from a text field.
- @NOCOPY-FORM – Prevents the user from copying from a text field on a data entry form.
- @NOCOPY-SURVEY – Prevents the user from copying from a text field on a survey.
- @NOPASTE – Prevents the user from pasting into a text field.
- @NOPASTE-FORM – Prevents the user from pasting into a text field on a data entry form.
- @NOPASTE-SURVEY – Prevents the user from pasting into a text field on a survey.

If a user attempts to copy (or drag) from, or paste (or drop) into, a field with the appropriate tag in the right context, they will be silently prevented from doing so. This may be useful for protecting notes fields from being inadvertently copied into the local computer's clipboard, or forcing a user to enter data manually rather than copying and pasting from another field, for example, on 'confirm your email address' fields where copying and pasting an incorrect email address would result in lost participants.

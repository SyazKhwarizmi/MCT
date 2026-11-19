/**
 * Paste this into your Google Sheet:
 * Extensions > Apps Script
 *
 * Then Deploy > New deployment > Web app
 * - Execute as: Me
 * - Who has access: Anyone
 *
 * Copy the web app URL into config.js as validationScriptUrl
 */

function doGet(e) {
  try {
    const action = String((e && e.parameter && e.parameter.action) || '').toLowerCase();
    if (action !== 'setvalidation') {
      return jsonResponse({ ok: false, error: 'Unknown action' });
    }

    const row = parseInt((e.parameter.row || ''), 10);
    const status = String(e.parameter.status || '').trim();
    const allowed = ['valid', 'reject', 'kiv', ''];
    if (!row || row < 2) {
      return jsonResponse({ ok: false, error: 'Invalid row' });
    }
    if (!allowed.includes(status.toLowerCase())) {
      return jsonResponse({ ok: false, error: 'Status must be valid, reject, kiv, or empty' });
    }

    const sheetName = String(e.parameter.sheet || 'submission');
    const ss = SpreadsheetApp.getActiveSpreadsheet();
    const sheet = ss.getSheetByName(sheetName);
    if (!sheet) {
      return jsonResponse({ ok: false, error: 'Sheet not found: ' + sheetName });
    }

    const headers = sheet.getRange(1, 1, 1, sheet.getLastColumn()).getValues()[0];
    let validationCol = -1;
    for (let i = 0; i < headers.length; i++) {
      const name = String(headers[i] || '').toLowerCase().trim();
      if (name === 'validation' || name === 'status') {
        validationCol = i + 1;
        break;
      }
    }
    if (validationCol < 0) {
      return jsonResponse({ ok: false, error: 'Validation column not found' });
    }

    const normalized =
      status === '' ? '' :
      status.toLowerCase() === 'rejected' ? 'reject' :
      status.toLowerCase();

    sheet.getRange(row, validationCol).setValue(normalized);
    return jsonResponse({ ok: true, row: row, status: normalized });
  } catch (err) {
    return jsonResponse({ ok: false, error: String(err) });
  }
}

function jsonResponse(payload) {
  return ContentService
    .createTextOutput(JSON.stringify(payload))
    .setMimeType(ContentService.MimeType.JSON);
}

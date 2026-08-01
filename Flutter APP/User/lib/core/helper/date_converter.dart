import 'package:intl/intl.dart';
import 'package:ovopay/core/data/services/service_exporter.dart';
import 'package:timezone/timezone.dart' as tz;

/// Enum for predefined format types
enum DateFormatType { onlyDate, onlyTime, dateTime12hr, dateTime24hr }

class DateConverter {
  static final _isoFormat = DateFormat('yyyy-MM-ddTHH:mm:ss.SSS');

  /// Map enum to format string
  static String _getFormatFromType(DateFormatType type) {
    switch (type) {
      case DateFormatType.onlyDate:
        return 'dd MMM yyyy';
      case DateFormatType.onlyTime:
        return 'hh:mm aa';
      case DateFormatType.dateTime12hr:
        return 'dd MMM yyyy hh:mm:ss aa';
      case DateFormatType.dateTime24hr:
        return 'dd MMM yyyy HH:mm:ss';
    }
  }

  /// Get user timezone location, fallback to UTC
  static tz.Location _getUserTimeZone() {
    try {
      final timeZone = SharedPreferenceService.getGeneralSettingData().data?.generalSetting?.timezone ?? 'UTC';
      return tz.getLocation(timeZone);
    } catch (_) {
      return tz.getLocation('UTC');
    }
  }

  /// Format DateTime using user timezone and optional format or enum
  static String estimatedDateOrTime(
    dynamic dateTime, {
    String? customFormat,
    DateFormatType? formatType,
    bool isLocal = false,
  }) {
    try {
      // STEP 1: Convert input `dateTime` (string or datetime) to DateTime object
      DateTime parsedDate;

      if (dateTime is String) {
        // try ISO + fallback formats
        parsedDate = DateTime.tryParse(dateTime) ?? DateFormat("yyyy-MM-dd HH:mm:ss").parse(dateTime, true);
      } else if (dateTime is DateTime) {
        parsedDate = dateTime;
      } else {
        throw Exception("Invalid date type");
      }

      // STEP 2: User timezone
      final location = _getUserTimeZone();

      // STEP 3: Convert parsed date to user timezone
      final zonedTime = tz.TZDateTime.from(parsedDate, location);

      // STEP 4: Decide which time to format
      final dateToFormat = isLocal ? parsedDate : zonedTime.toLocal();

      // STEP 5: Format handler
      final format = customFormat ?? _getFormatFromType(formatType ?? DateFormatType.dateTime12hr);

      return DateFormat(format).format(dateToFormat);
    } catch (e) {
      // fallback
      final format = customFormat ?? _getFormatFromType(formatType ?? DateFormatType.dateTime12hr);

      try {
        if (dateTime is DateTime) {
          return DateFormat(format).format(dateTime.toLocal());
        } else {
          final fallbackDate = DateTime.parse(dateTime.toString());
          return DateFormat(format).format(fallbackDate.toLocal());
        }
      } catch (_) {
        return "";
      }
    }
  }

  /// Convert ISO string to DateTime in user timezone
  static DateTime isoStringToUserZone(String isoString) {
    final parsedUtc = _isoFormat.parse(isoString, true).toUtc();
    final location = _getUserTimeZone();
    return tz.TZDateTime.from(parsedUtc, location);
  }

  /// "x time ago" string from ISO string, using user timezone
  static String getTimeAgo(String isoTime) {
    final now = tz.TZDateTime.from(DateTime.now().toUtc(), _getUserTimeZone());
    final past = isoStringToUserZone(isoTime);
    final diff = now.difference(past);

    if (diff.inDays >= 365) {
      final years = (diff.inDays / 365).floor();
      return '$years year${years > 1 ? 's' : ''} ago';
    } else if (diff.inDays >= 30) {
      final months = (diff.inDays / 30).floor();
      return '$months month${months > 1 ? 's' : ''} ago';
    } else if (diff.inDays >= 7) {
      final weeks = (diff.inDays / 7).floor();
      return '$weeks week${weeks > 1 ? 's' : ''} ago';
    } else if (diff.inDays >= 1) {
      return '${diff.inDays} day${diff.inDays > 1 ? 's' : ''} ago';
    } else if (diff.inHours >= 1) {
      return '${diff.inHours} hour${diff.inHours > 1 ? 's' : ''} ago';
    } else if (diff.inMinutes >= 1) {
      return '${diff.inMinutes} minute${diff.inMinutes > 1 ? 's' : ''} ago';
    } else if (diff.inSeconds >= 3) {
      return '${diff.inSeconds} second${diff.inSeconds > 1 ? 's' : ''} ago';
    } else {
      return 'Just now';
    }
  }

  static String localTime(DateTime dateTime) {
    return DateFormat('hh:mm:ss a').format(dateTime);
  }

  static String localDate(DateTime dateTime) {
    return DateFormat('dd MMM yyyy').format(dateTime);
  }

  static String localDateTime(DateTime dateTime) {
    return DateFormat('dd MMM yyyy - hh:mm:ss a').format(dateTime);
  }
}

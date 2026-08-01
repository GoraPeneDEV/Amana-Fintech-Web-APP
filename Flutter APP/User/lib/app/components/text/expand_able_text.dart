import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

class ExpandableText extends StatefulWidget {
  final String text;
  final TextStyle style;
  final int charLimit; // Maximum number of characters to display

  const ExpandableText({
    super.key,
    required this.text,
    required this.style,
    this.charLimit = 200, // Default character limit
  });

  @override
  State<ExpandableText> createState() => _ExpandableTextState();
}

class _ExpandableTextState extends State<ExpandableText> {
  bool isExpanded = false;

  @override
  Widget build(BuildContext context) {
    final isTooLong = widget.text.length > widget.charLimit;

    final displayText = isTooLong && !isExpanded ? '${widget.text.substring(0, widget.charLimit)}...' : widget.text;

    return RichText(
      text: TextSpan(
        text: displayText,
        style: widget.style,
        children: isTooLong
            ? [
                TextSpan(
                  text: isExpanded ? ' ${MyStrings.less.tr}' : ' ${MyStrings.more.tr}',
                  style: widget.style.copyWith(
                    color: MyColor.getPrimaryColor(),
                  ),
                  recognizer: TapGestureRecognizer()
                    ..onTap = () {
                      setState(() {
                        isExpanded = !isExpanded;
                      });
                    },
                ),
              ]
            : [],
      ),
    );
  }
}

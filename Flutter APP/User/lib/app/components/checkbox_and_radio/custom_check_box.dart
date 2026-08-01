import 'package:flutter/material.dart';
import 'package:ovopay/app/components/text/header_text.dart';

import '../../../core/utils/util_exporter.dart';

class CustomCheckBox extends StatefulWidget {
  final List<String>? selectedValue;
  final List<String> list;
  final ValueChanged? onChanged;
  final String? title;
  final String? instruction;
  final bool isRequired;

  const CustomCheckBox({
    super.key,
    this.selectedValue,
    required this.list,
    this.onChanged,
    this.title,
    this.instruction,
    this.isRequired = false,
  });

  @override
  State<CustomCheckBox> createState() => _CustomCheckBoxState();
}

class _CustomCheckBoxState extends State<CustomCheckBox> {
  final GlobalKey<TooltipState> _tooltipKey = GlobalKey<TooltipState>();
  @override
  Widget build(BuildContext context) {
    if (widget.list.isEmpty) {
      return Container();
    }
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        widget.title == null
            ? const SizedBox()
            : Row(
                children: [
                  HeaderText(
                    text: (widget.title ?? "").toCapitalized(),
                    textStyle: MyTextStyle.sectionTitle3.copyWith(
                      color: MyColor.getHeaderTextColor(),
                    ),
                  ),
                  if (widget.instruction != null) ...[
                    spaceSide(Dimensions.space5),
                    Tooltip(
                      onTriggered: () {},
                      key: _tooltipKey,
                      message: "${widget.instruction}",
                      child: InkWell(
                        onTap: () {
                          _tooltipKey.currentState?.ensureTooltipVisible();
                        },
                        child: Icon(
                          Icons.info_outline_rounded,
                          size: Dimensions.space18,
                          color: MyColor.getBodyTextColor(),
                        ),
                      ),
                    ),
                  ],
                  if (widget.isRequired) ...[
                    spaceSide(Dimensions.space5),
                    Text(
                      "*",
                      style: MyTextStyle.sectionSubTitle1.copyWith(
                        color: MyColor.error,
                      ),
                    ),
                  ],
                ],
              ),
        Column(
          children: List<CheckboxListTile>.generate(widget.list.length, (
            int index,
          ) {
            List<String>? s = widget.selectedValue;
            bool selected_ = s != null ? s.contains(widget.list[index]) : false;
            return CheckboxListTile(
              value: selected_,
              activeColor: MyColor.getPrimaryColor(),
              title: Text(
                widget.list[index],
                style: MyTextStyle.sectionSubTitle1.copyWith(
                  color: MyColor.getBodyTextColor(),
                ),
              ),
              onChanged: (bool? value) {
                setState(() {
                  if (value != null) {
                    widget.onChanged!('${index}_$value');
                  }
                });
              },
            );
          }),
        ),
      ],
    );
  }
}

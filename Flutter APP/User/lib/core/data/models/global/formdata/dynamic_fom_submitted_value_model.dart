class UsersDynamicFormSubmittedDataModel {
  String? name;
  String? type;
  String? value;
  List<String>? checkboxValues;

  UsersDynamicFormSubmittedDataModel({
    this.name,
    this.type,
    this.value,
    this.checkboxValues,
  });

  factory UsersDynamicFormSubmittedDataModel.fromJson(
    Map<String, dynamic> json,
  ) {
    String? type = json["type"]?.toString();
    dynamic rawValue = json["value"];

    if (type == "checkbox") {
      return UsersDynamicFormSubmittedDataModel(
        name: json["name"]?.toString(),
        type: type,
        checkboxValues: rawValue == null ? [] : List<String>.from(rawValue.map((e) => e.toString())),
      );
    } else {
      return UsersDynamicFormSubmittedDataModel(
        name: json["name"]?.toString(),
        type: type,
        value: rawValue?.toString(),
      );
    }
  }

  Map<String, dynamic> toJson() {
    return {
      "name": name,
      "type": type,
      "value": type == "checkbox" ? checkboxValues : value,
    };
  }
}

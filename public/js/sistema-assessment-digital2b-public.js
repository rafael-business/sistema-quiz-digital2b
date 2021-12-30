(function ($) {
  "use strict";
  var actualStep = 1;
  var errorMsg = [
    "Selecione uma opção antes de continuar!",
    "Você deve preencher todos os campos para continuar!",
  ];
  var canIContinue = true;

  function getAnswersByType(parentElement, fieldType) {
    if (fieldType == "radio_resposta") {
      return $(parentElement).find(".selectedOption p").html();
    } else if (fieldType == "texto_resposta") {
      return $(parentElement).find("input[type='text']").val();
    } else if (fieldType == "checkbox_resposta") {
      let currentAnswers = [];
      $(parentElement)
        .find("input:checked")
        .each(function () {
          currentAnswers.push($(this).val());
        });
      return currentAnswers;
    } else if (fieldType == "imagem_resposta") {
      return $(parentElement).find("input[type='file']").val();
    }
  }
  function getFormFields() {
    let formatedFormData = [];
    $(".assessment-question-content form .step-content").each(function () {
      var parentElement = $(this);
      var fieldType = $(this).attr("field-type");
      var fieldValue = getAnswersByType(parentElement, fieldType);
      formatedFormData.push({
        title: $(this).find(".question-title span").html(),
        answers: fieldValue,
      });
    });

    return formatedFormData;
  }

  setTimeout(() => {
    $(".finish-assessment").on("click", function (e) {
      canIContinue = true;

      e.preventDefault();

      $(".finish-assessment-content input")
        .not($("input[type=hidden]"))
        .each(function () {
          if ($(this).val() == "" || !$(this).val()) {
            console.log($(this));
            canIContinue = false;

            $(".error-step").html(errorMsg[1]);
            $(".error-step").addClass("active");

            setTimeout(() => {
              $(".error-step").removeClass("active");
              $(".error-step").html(errorMsg[0]);
            }, 2900);

            return;
          }
        });
      //Valor dos campos

      var formFields = {
        fieldText: [],
        fieldRadio: [],
        fieldImg: [],
        fieldCheckbox: [],
        tituloPergunta: [],
      };


      // setTimeout(() => {
      //   $.ajax({
      //     url: digital2b_scripts.ajax,
      //     data: {
      //       action: "addMetaRespostas",
      //       assessmentid: $(this).attr("assessmentid"),
      //       answers: getFormFields(),
      //     },
      //     type: "POST",
      //     success: function (data) {
      //       console.log("sucesso!");

      //       var response = JSON.parse(data);
      //       console.log(response);

      //     },
      //     beforeSend: function (d) {
          
      //     },
      //   });

      // }, 1200);
    });

    var maxSteps = $(".step-content").length;

    $(".start-assessment").on("click", function (e) {
      actualStep = 1;
      $(".assessment-question-box").addClass("active-step").removeClass("inactive");
      $(".assessment-presentation").removeClass("active-step").addClass("inactive");
      $(".step-content")
        .not($(".step-" + actualStep))
        .removeClass("active-step")
        .addClass("inactive");
      $(".step-" + actualStep).addClass("active-step");
    });

    $(".step-content .answers-box li").on("click", function () {
      $(".step-" + $(this).attr("step") + " li")
        .not($(this))
        .removeClass("selectedOption");
      $(this).toggleClass("selectedOption");
    });

    function verifyFieldByType(fieldType) {
      if (fieldType == "radio_resposta") {
        return $(".step-" + actualStep + " .selectedOption").length > 0;
      } else if (fieldType == "texto_resposta") {
        return $(".step-" + actualStep + " input").val().length > 0;
      } else if (fieldType == "checkbox_resposta") {
        return $(".step-" + actualStep + " input:checked").length > 0;
      } else if (fieldType == "imagem_resposta") {
        return $(".step-" + actualStep + " input").get(0).files.length > 0;
      }
    }

    $(".step-footer").delegate("a", "click", function (e) {
      e.preventDefault();

      var fieldType = $(
        ".step-" + actualStep + ".step-content.active-step"
      ).attr("field-type");

      canIContinue = verifyFieldByType(fieldType);

      if (
        !canIContinue &&
        !$(this).hasClass("prev-step") &&
        !$(this).hasClass("finish-assessment")
      ) {
        $(".error-step").addClass("active");

        setTimeout(() => {
          $(".error-step").removeClass("active");
        }, 2900);

        return;
      }

      if (!$(this).hasClass("finish-assessment")) {
        if ($(this).hasClass("next-step")) {
          $(".step-content")
            .not($(".step-" + actualStep))
            .removeClass("active-step")
            .addClass("inactive");
          $(".step-" + actualStep).removeClass("active-step");
          actualStep++;
          $(".step-" + actualStep).addClass("active-step");
        } else {
          if (actualStep - 1 >= 1) {
            $(".step-" + actualStep).removeClass("active-step");
            actualStep--;
            $(".step-" + actualStep).addClass("active-step");
          } else {
            actualStep = 0;
            $(".assessment-question-box")
              .removeClass("active-step")
              .addClass("inactive");
            $(".assessment-presentation")
              .addClass("active-step")
              .removeClass("inactive");
          }
        }

        if (actualStep == maxSteps + 1) {
          $(".finish-assessment-content").addClass("active");
          $(".next-step").hide();
          $(".finish-assessment").show();
        } else {
          $(".finish-assessment-content").removeClass("active");
          $(".next-step").show();
          $(".finish-assessment").hide();
        }
      }
    });
  }, 300);
})(jQuery);

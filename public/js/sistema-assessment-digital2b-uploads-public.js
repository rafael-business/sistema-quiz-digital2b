(function ($) {
    "use strict";
    var actualStep = 1;
    var formData = new FormData();

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
        var files = $(parentElement).find('input')[0].files;
        var fileTitle = $(parentElement).find(".question-title span").html();
        for (var i = 0; i < files.length; i++) {
            formData.append(fileTitle, files[i])
        }
      }
    }

    function getFormFields() {
      $(".assessment-question-content form .step-content").each(function () {
        var parentElement = $(this);
        var fieldType = $(this).attr("field-type");
        var fieldValue = getAnswersByType(parentElement, fieldType);

        if(fieldType !== "imagem_resposta") {
            formData.append('answers[]['+ $(this).find(".question-title span").html() + ']', fieldValue);
        }
      });
  
    }
  
    setTimeout(() => {
      $(".finish-assessment").on("click", function (e) {
        formData = new FormData();
        canIContinue = true;

        e.preventDefault();

        var formFields = getFormFields();
       
        formData.append('assessmentid', $(this).attr("assessmentid"));
        formData.append('action','saveassessmentContent');
        
  
          $.ajax({
            url: digital2b_scripts.ajax,
            data : formData,
            processData: false,
            contentType: false,
            type: "POST",
            success: function (data) {
              console.log("sucesso!");
              console.log(data);
              var response = JSON.parse(data);
              console.log(response);
  
             
            },
            beforeSend: function (d) {
            
            },
          });

        });
    }, 1200);
  
  
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
  
  })(jQuery);
  
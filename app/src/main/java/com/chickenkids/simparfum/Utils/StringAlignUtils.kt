package com.chickenkids.simparfum.Utils

import java.text.FieldPosition
import java.text.Format
import java.text.ParsePosition
import java.util.*

class StringAlignUtils(
    /** Current max length in a line  */
    private val maxChars: Int, align: Alignment
) : Format() {

    /** Current justification for formatting  */
    private var currentAlignment: Alignment? = null

    enum class Alignment {
        LEFT, CENTER, RIGHT
    }

    init {
        when (align) {
            Alignment.LEFT, Alignment.CENTER, Alignment.RIGHT -> this.currentAlignment =
                align
            else -> throw IllegalArgumentException("invalid justification arg.")
        }
        require(maxChars >= 0) { "maxChars must be positive." }
    }

    override fun format(input: Any, where: StringBuffer, ignore: FieldPosition): StringBuffer {
        val s = input.toString()
        val strings = splitInputString(s)
        val listItr = strings.listIterator()

        while (listItr.hasNext()) {
            val wanted = listItr.next()

            //Get the spaces in the right place.
            when (currentAlignment) {
                Alignment.RIGHT -> {
                    pad(where, maxChars - wanted.length)
                    where.append(wanted)
                }
                Alignment.CENTER -> {
                    val toAdd = maxChars - wanted.length
                    pad(where, toAdd / 2)
                    where.append(wanted)
                    pad(where, toAdd - toAdd / 2)
                }
                Alignment.LEFT -> {
                    where.append(wanted)
                    pad(where, maxChars - wanted.length)
                }
            }
            where.append("\n")
        }
        return where
    }

    protected fun pad(to: StringBuffer, howMany: Int) {
        for (i in 0 until howMany)
            to.append(' ')
    }

    internal fun format(s: String): String {
        return format(s, StringBuffer(), null!!).toString()
    }

    /** ParseObject is required, but not useful here.  */
    override fun parseObject(source: String, pos: ParsePosition): Any {
        return source
    }

    private fun splitInputString(str: String?): List<String> {
        val list = ArrayList<String>()
        if (str == null)
            return list
        var i = 0
        while (i < str.length) {
            val endindex = Math.min(i + maxChars, str.length)
            list.add(str.substring(i, endindex))
            i = i + maxChars
        }
        return list
    }

    companion object {

        private val serialVersionUID = 1L
    }
}